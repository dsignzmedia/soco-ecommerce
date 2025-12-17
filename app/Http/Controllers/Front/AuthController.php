<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller
{
    /**
     * Initiate Razorpay Order
     */
    public function initiateRazorpay(Request $request)
    {
        $user = Auth::user();
        $total = $request->total;

        // Fetch Razorpay credentials from database
        $gateway = \App\Models\Admin\Master\PaymentGateway::where('provider', 'razorpay')
            ->where('is_active', true)
            ->first();

        $keyId = null;
        $keySecret = null;

        if ($gateway && !empty($gateway->credentials)) {
            $keyId = $gateway->credentials['key_id'] ?? ($gateway->credentials['key'] ?? null);
            $keySecret = $gateway->credentials['key_secret'] ?? ($gateway->credentials['secret'] ?? null);
        }

        // Fallback to .env if not configured in DB
        if (empty($keyId) || empty($keySecret)) {
            $keyId = env('RAZORPAY_KEY');
            $keySecret = env('RAZORPAY_SECRET');
        }

        if (empty($keyId) || empty($keySecret)) {
            return response()->json(['success' => false, 'message' => 'Payment gateway not configured.']);
        }

        // Create order via Razorpay API
        try {
            $amountInPaise = (int)($total * 100);
            Log::info('Razorpay Init - Requesting Order', [
                'total_input' => $total,
                'amount_paise' => $amountInPaise,
                'key_source' => $gateway ? 'database' : 'env' // helpful for debugging
            ]);

            // Disable SSL verification only in local environment
            $response = Http::withOptions(['verify' => !app()->isLocal()])
                ->withBasicAuth($keyId, $keySecret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise, // Amount in paise
                    'currency' => 'INR',
                    'receipt' => 'order_rcptid_' . time(),
                    'payment_capture' => 1
                ]);

            $order = $response->json();

            Log::info('Razorpay Init - Response', ['status' => $response->status(), 'body' => $order]);

            if (isset($order['id'])) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order['id'],
                    'key' => $keyId,
                    'amount' => $order['amount'],
                    'name' => 'The Skool Store',
                    'description' => 'Order Payment',
                    'prefill' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'email' => $user->email,
                        'contact' => (function($phone) {
                            $p = preg_replace('/[^0-9]/', '', $phone);
                            if (strlen($p) > 10 && substr($p, 0, 2) === '91') {
                                return substr($p, 2);
                            }
                            return $p;
                        })($user->phone ?? session('parent_phone')),
                    ]
                ]);
            } else {
                Log::error('Razorpay Order Creation Failed', ['response' => $order]);
                return response()->json(['success' => false, 'message' => 'Could not initiate payment.']);
            }
        } catch (\Exception $e) {
            Log::error('Razorpay Exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Payment initialization error.']);
        }
    }

    /**
     * Verify Razorpay Payment and Place Order
     */
    public function verifyRazorpay(Request $request)
    {
        $signature = $request->razorpay_signature;
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id;

        // Fetch Razorpay credentials from database
        $gateway = \App\Models\Admin\Master\PaymentGateway::where('provider', 'razorpay')
            ->where('is_active', true)
            ->first();

        $keySecret = null;
        if ($gateway && !empty($gateway->credentials)) {
            $keySecret = $gateway->credentials['key_secret'] ?? ($gateway->credentials['secret'] ?? null);
        }

        // Fallback to .env
        if (empty($keySecret)) {
            $keySecret = env('RAZORPAY_SECRET');
        }

        if (empty($keySecret)) {
            return response()->json(['success' => false, 'message' => 'Payment configuration error.']);
        }

        $generatedSignature = hash_hmac('sha256', $orderId . "|" . $paymentId, $keySecret);

        if ($generatedSignature === $signature) {
            // Payment successful

            // Fetch Key ID for API call
            $keyId = null;
            if ($gateway && !empty($gateway->credentials)) {
                $keyId = $gateway->credentials['key_id'] ?? ($gateway->credentials['key'] ?? null);
            }
            if (empty($keyId)) {
                $keyId = env('RAZORPAY_KEY');
            }

            // Fetch Payment Details from Razorpay
            $paymentDetails = [];
            $amountPaid = 0;

            try {
                $response = Http::withOptions(['verify' => !app()->isLocal()])
                    ->withBasicAuth($keyId, $keySecret)
                    ->get('https://api.razorpay.com/v1/payments/' . $paymentId);

                if ($response->successful()) {
                    $paymentDetails = $response->json();
                    $amountPaid = ($paymentDetails['amount'] ?? 0) / 100;
                }
            } catch (\Exception $e) {
                Log::error('Razorpay Payment Fetch Error', ['error' => $e->getMessage()]);
            }

            // Add payment info to session
            session([
                'payment_method' => 'razorpay',
                'payment_id' => $paymentId,
                'amount_paid' => $amountPaid,
                'payment_details' => $paymentDetails
            ]);

            // Call processCheckout
            return $this->processCheckout($request);

        } else {
            return response()->json(['success' => false, 'message' => 'Payment verification failed.']);
        }
    }

    /**
     * Send OTP for login/registration
     */
    public function sendOtp(Request $request, \App\Services\SmsService $smsService)
    {
        $request->validate([
            'contact' => 'required|string',
        ]);

        $contact = $request->contact;

        // Strict Validation Logic
        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);
        $isPhone = preg_match('/^[0-9]{10}$/', $contact);

        if (!$isEmail && !$isPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address or a 10-digit mobile number.',
            ], 422);
        }

        $field = $isEmail ? 'email' : 'phone';

        // Check if user exists
        $user = User::where($field, $contact)->first();
        $isNewUser = !$user;

        if (!$user) {
            // Create a temporary/guest user to store the OTP
            // We'll update the name later during registration if needed
            $user = User::create([
                'name' => 'Guest',
                'email' => $isEmail ? $contact : null, // Allow null email if phone
                'phone' => $isEmail ? null : $contact,
                'password' => bcrypt(str()->random(16)),
                'role' => User::ROLE_PARENT,
            ]);
        }

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store in Users table
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->otp_verified = false; // Reset verification status
        $user->save();

        Log::info("OTP generated for {$contact}");

        // Send OTP via SMS or Email
        if ($isEmail) {
            try {
                \Illuminate\Support\Facades\Mail::to($contact)->send(new \App\Mail\OtpMail($otp));
                Log::info("Email OTP sent to {$contact}");
            } catch (\Exception $e) {
                Log::error("Failed to send email OTP to {$contact}: " . $e->getMessage());
                // Fallback or just log error? For now, we log.
            }
        } else {
            $message = "Your OTP for The Skool Store is: {$otp}. Valid for 10 minutes.";
            $smsService->send($contact, $message);
        }

        return response()->json([
            'success' => true,
            'is_new_user' => $isNewUser,
            'message' => 'OTP sent successfully!',
            // 'otp' => $otp, // REMOVED FOR SECURITY
            'contact_type' => $field,
        ]);
    }

    /**
     * Verify OTP and login/register
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
            'contact' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);

        $otp = $request->otp;
        $contact = $request->contact;

        $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'phone';

        // Find user
        $user = User::where($field, $contact)->first();

        if (!$user || $user->otp != $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired.',
            ], 422);
        }

        // OTP is valid
        // Mark as verified
        $user->otp_verified = true;
        // Optional: Clear OTP to prevent reuse, but maybe keep for audit?
        // Let's clear it to be safe and clean
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // If name is provided and user was a guest (name is Guest), update name
        if ($request->name && $user->name === 'Guest') {
            $user->name = $request->name;
            $user->save();
        }

        Auth::login($user);

        // Store parent phone
        if ($user->phone) {
            session(['parent_phone' => $user->phone]);
        } elseif ($user->email) {
            session(['parent_phone' => $user->email]);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => $this->redirectBasedOnRole($user),
        ]);
    }



    // Unified login page for both parents and schools
    public function showLogin()
    {
        return view('frontend.auth.login');
    }

    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google OAuth - User data received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId(),
            ]);
        } catch (\Exception $e) {
            Log::error('Google OAuth - Failed to get user', ['error' => $e->getMessage()]);
            return redirect()->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }

        // Extract username from email (part before @)
        $email = $googleUser->getEmail();
        $username = explode('@', $email)[0];

        Log::info('Google OAuth - Extracted data', [
            'email' => $email,
            'username' => $username,
        ]);

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // User exists - Log them in
            Log::info('Google OAuth - User found, logging in', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            Auth::login($user, true);

            // Store parent phone (optional)
            session(['parent_phone' => $user->phone ?? $user->email]);

            return redirect($this->redirectBasedOnRole($user))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        } else {
            // User does not exist - Create new user and login
            Log::info('Google OAuth - User not found, creating new user', [
                'email' => $email,
            ]);

            $user = User::create([
                'name' => $googleUser->getName() ?? $username,
                'email' => $email,
                'password' => bcrypt(str()->random(16)), // Random password
                'role' => User::ROLE_PARENT, // Default role for new users
                'email_verified_at' => now(),
            ]);

            Log::info('Google OAuth - New user created', [
                'user_id' => $user->id,
            ]);

            Auth::login($user, true);

            // Store parent phone (optional)
            session(['parent_phone' => $user->phone ?? $user->email]);

            return redirect($this->redirectBasedOnRole($user))
                ->with('success', 'Registration successful! Welcome to The Skool Store.');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('frontend.auth.register');
    }

    /**
     * Handle registration submission
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt', $request->all());

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
            ]);

            Log::info('Validation passed', $validated);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => bcrypt($validated['password']),
                'role' => User::ROLE_PARENT, // Default role for new users
            ]);

            Log::info('User created', $user->toArray());

            Auth::login($user);

            // Store parent phone in session for dashboard compatibility
            if ($user->phone) {
                session(['parent_phone' => $user->phone]);
            } else {
                session(['parent_phone' => $user->email]);
            }

            return redirect()->route('frontend.parent.dashboard')
                ->with('success', 'Registration successful! Welcome to The Skool Store.');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Handle login submission - Unified login for all user types
     */
    public function login(Request $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            Log::info('Login successful', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'role' => $user->role,
                'role_type' => gettype($user->role),
                'isSchool()' => $user->isSchool() ? 'YES' : 'NO',
                'ROLE_SCHOOL_CONSTANT' => User::ROLE_SCHOOL,
            ]);

            // Store parent phone in session for dashboard compatibility
            if ($user->phone) {
                session(['parent_phone' => $user->phone]);
            } else {
                session(['parent_phone' => $user->email]);
            }

            // Login parent users via web guard
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Auth::user();

                Log::info('Parent login successful', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $user->name,
                    'role' => $user->role,
                ]);

                // Store parent phone in session for dashboard compatibility
                if ($user->phone) {
                    session(['parent_phone' => $user->phone]);
                } else {
                    session(['parent_phone' => $user->email]);
                }

                // Redirect to parent dashboard
                return redirect()->route('frontend.parent.dashboard');
            }
        }

        Log::warning('Login failed - Invalid credentials', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // DO NOT invalidate the entire session as it would logout Master/Inventory Admin users too
        // $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.index')
            ->with('success', 'You have been logged out successfully.');
    }

    public function authenticateSchool(Request $request)
    {
        // Redirect to unified login - schools now use email/password like everyone else
        // This method is kept for backward compatibility but redirects to main login

        Log::info('School login attempt - redirecting to unified login');

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to authenticate using email (username field contains email)
        $credentials = [
            'email' => $request->username,
            'password' => $request->password,
        ];

        if (auth('school')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            Log::info('School login successful', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            // Use role-based redirect
            return $this->redirectBasedOnRole($user);
        }

        Log::warning('School login failed - Invalid credentials', ['username' => $request->username]);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function schoolDashboard()
    {
        // Middleware already ensures user is logged in and is a school
        $user = Auth::user();

        if (!$user) {
             return redirect()->route('login');
        }

        // Get school data from database
        $school = $user->school;

        if (!$school) {
            // If the authenticated school user has no associated school record,
            // redirect them to the school login page with an informative message.
            return redirect()->route('login')
                ->with('error', 'School profile not found. Please complete your school setup or contact administrator.');
        }

        // Get real order statistics for this school
        $orders = \App\Models\Admin\Master\Order::where('school_id', $school->id);

        $dashboardData = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'pending_orders' => $orders->where('order_status', 'pending')->count(),
            'completed_orders' => $orders->whereIn('order_status', ['completed', 'delivered'])->count(),
        ];

        $schoolName = $school->name;
        $schoolAddress = $school->city ?? '';

        return view('frontend.dashboard.school-dashboard', compact('dashboardData', 'schoolName', 'schoolAddress'));
    }

    public function schoolOrders(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user || !$user->isSchool()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'School not found. Please contact administrator.');
        }

        // Get orders for this school with filters
        $query = \App\Models\Admin\Master\Order::where('school_id', $school->id)
            ->orderBy('order_date', 'desc');

        // Apply status filter if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        // Apply search filter if provided
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        // Analytics Data
        $analytics = [
            'total_orders' => \App\Models\Admin\Master\Order::where('school_id', $school->id)->count(),
            'total_revenue' => \App\Models\Admin\Master\Order::where('school_id', $school->id)->sum('total_amount'),
            'pending_orders' => \App\Models\Admin\Master\Order::where('school_id', $school->id)->where('order_status', 'pending')->count(),
            'completed_orders' => \App\Models\Admin\Master\Order::where('school_id', $school->id)->whereIn('order_status', ['completed', 'delivered'])->count(),
            'orders_by_grade' => \App\Models\Admin\Master\Order::where('school_id', $school->id)
                ->selectRaw('grade, count(*) as count')
                ->groupBy('grade')
                ->pluck('count', 'grade'),
            'orders_by_month' => \App\Models\Admin\Master\Order::where('school_id', $school->id)
                ->selectRaw('MONTHNAME(order_date) as month, count(*) as count')
                ->groupBy('month')
                ->pluck('count', 'month'),
        ];

        return view('frontend.dashboard.school-orders', compact('orders', 'school', 'analytics'));
    }

    public function schoolStudents(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user || !$user->isSchool()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'School not found. Please contact administrator.');
        }

        // Get students for this school
        // We link StudentProfile to School via school_name
        $query = \App\Models\StudentProfile::where('school_name', $school->name)
            ->with('user'); // Eager load parent

        // Apply filters
        if ($request->has('grade') && $request->grade != '') {
            $query->where('grade', $request->grade);
        }

        if ($request->has('section') && $request->section != '') {
            $query->where('section', $request->section);
        }

        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        // Apply search filter if provided
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('grade', 'like', "%{$search}%");
            });
        }

        // Custom Sort Order
        $gradeOrderSql = "CASE 
            WHEN grade = 'PKG' THEN 1 
            WHEN grade = 'LKG' THEN 2 
            WHEN grade = 'UKG' THEN 3
            WHEN grade REGEXP '^[0-9]+$' THEN CAST(grade AS UNSIGNED) + 3
            ELSE 999 
        END";

        $students = $query->orderByRaw($gradeOrderSql)->orderBy('student_name')->paginate(20);

        // Get filter options sorted logically
        $grades = \App\Models\StudentProfile::where('school_name', $school->name)
            ->whereNotNull('grade')
            ->distinct()
            ->get()
            ->pluck('grade')
            ->sort(function ($a, $b) {
                $order = [
                    'PKG' => 1, 'LKG' => 2, 'UKG' => 3,
                    '1' => 4, '2' => 5, '3' => 6, '4' => 7, '5' => 8, '6' => 9,
                    '7' => 10, '8' => 11, '9' => 12, '10' => 13, '11' => 14, '12' => 15
                ];
                
                $valA = $order[$a] ?? 999;
                $valB = $order[$b] ?? 999;
                
                return $valA <=> $valB;
            });

        $sections = \App\Models\StudentProfile::where('school_name', $school->name)
            ->whereNotNull('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');

        return view('frontend.dashboard.school-students', compact('students', 'school', 'grades', 'sections'));
    }




    public function schoolProducts(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user || !$user->isSchool()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'School not found. Please contact administrator.');
        }

        // Get products for this school
        $query = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id);

        // Apply filters
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('product_type') && $request->product_type != '') {
            $query->where('product_type', $request->product_type);
        }

        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        // Apply search filter if provided
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('product_name', 'like', "%{$search}%");
        }

        $products = $query->paginate(20);

        // Get filter options
        $categories = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $productTypes = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id)
            ->whereNotNull('product_type')
            ->distinct()
            ->orderBy('product_type')
            ->pluck('product_type');

        $genders = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id)
            ->whereNotNull('gender')
            ->distinct()
            ->orderBy('gender')
            ->pluck('gender');

        return view('frontend.dashboard.school-products', compact('products', 'school', 'categories', 'productTypes', 'genders'));
    }



    public function schoolSettings()
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user || !$user->isSchool()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'School not found. Please contact administrator.');
        }

        return view('frontend.dashboard.school-settings', compact('school'));
    }

    public function updateSchoolSettings(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user || !$user->isSchool()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $school = $user->school;

        if (!$school) {
            return redirect()->route('login')
                ->with('error', 'School not found. Please contact administrator.');
        }

        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ]);

        $school->update([
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address, // Assuming 'address' field exists or maps to something
            'city' => $request->city,
            'state' => $request->state,
            // 'zip_code' => $request->zip_code, // Check if this column exists in School model
        ]);

        return redirect()->route('frontend.school.settings')
            ->with('success', 'School settings updated successfully.');
    }

    public function schoolReports()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $school = $user->school;

        if (!$school) {
            return redirect()->route('frontend.school.dashboard');
        }

        // Fetch dynamic filter options
        $grades = \App\Models\Admin\Master\Order::where('school_id', $school->id)
            ->whereNotNull('grade')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $products = \App\Models\Admin\Master\Order::where('school_id', $school->id)
            ->distinct()
            // In a real scenario, we might join order_items, but for now we might not have product names directly on orders or
            // we have to check how orders are structured.
            // The Order model has 'category', maybe use that? Or we can't easily get distinct products from orders if it's JSON or related table.
            // Let's use Category for now as 'Product' filter or fetch from ProductMapping.
            ->pluck('category') // As a proxy if product names aren't simple
            ->unique()
            ->values();

        // Better: Get products from ProductMapping for this school
        $products = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id)
            ->orderBy('product_name')
            ->pluck('product_name');

        // Auto-generate a default report (current month) if no report data in session
        if (!session('report_generated')) {
             // Create a mock request/data to generate default report
             $request = new Request([
                 'month' => date('n'),
                 'year' => date('Y'),
                 'grade' => null,
                 'product' => null
             ]);
             return $this->generateReport($request);
        }

        return view('frontend.dashboard.school-reports', compact('grades', 'products'));
    }

    public function generateReport(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $school = $user->school;

        // Validate filters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'grade' => 'nullable|string',
            'product' => 'nullable|string',
            'sale_type' => 'nullable|string',
        ]);

        // Store filter data in session for report generation
        $filters = $request->only(['start_date', 'end_date', 'grade', 'product', 'sale_type']);
        session(['report_filters' => $filters]);

        // Query Orders
        $query = \App\Models\Admin\Master\Order::where('school_id', $school->id);

        if (!empty($filters['start_date'])) {
            $query->whereDate('order_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('order_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }

        // Product filter
        if (!empty($filters['product'])) {
             $query->whereHas('items', function($q) use ($filters) {
                 $q->where('product_name', $filters['product']);
             });
        }

        // Status filter
        if (!empty($filters['sale_type'])) {
             $query->where('order_status', $filters['sale_type']);
        }

        $orders = $query->with('items')->orderBy('order_date')->get();

        // Calculate Aggregates
        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Find top product
        $topProduct = 'N/A';
        $productCounts = [];

        foreach ($orders as $order) {
            if ($order->items) {
                foreach ($order->items as $item) {
                    $pName = $item->product_name ?? 'Unknown Product';
                    if (!isset($productCounts[$pName])) {
                        $productCounts[$pName] = 0;
                    }
                    $productCounts[$pName] += $item->quantity ?? 1;
                }
            }
        }

        if (!empty($productCounts)) {
            arsort($productCounts);
            $topProduct = array_key_first($productCounts);
        }

        // Prepare Chart Data
        $chartLabels = [];
        $chartData = [];

        // Daily breakdown within the range
        $grouped = $orders->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->order_date)->format('d M');
        });

        foreach($grouped as $label => $grp) {
            $chartLabels[] = $label;
            $chartData[] = $grp->sum('total_amount');
        }

        $reportData = [
            'filters' => $filters,
            'summary' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => $avgOrderValue,
                'top_product' => $topProduct,
            ],
            'chart_data' => [
                'labels' => $chartLabels,
                'sales' => $chartData,
            ],
        ];

        session(['report_data' => $reportData]);

        return redirect()->route('frontend.school.reports')
            ->with('report_generated', true);
    }

    public function downloadReport(Request $request)
    {
        // Check if school is authenticated
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to download reports.');
        }

        $format = $request->get('format', 'excel'); // excel or pdf
        $reportData = session('report_data', []);

        if (empty($reportData)) {
            return redirect()->back()->with('error', 'No report data found. Please generate a report first.');
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('frontend.dashboard.reports-pdf', [
                'filters' => $reportData['filters'] ?? [],
                'summary' => $reportData['summary'] ?? [],
                'chart_data' => $reportData['chart_data'] ?? []
            ]);
            return $pdf->download('school-report-' . date('Y-m-d') . '.pdf');
        }

        // Generate CSV for Excel
        $fileName = 'school-report-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Summary Section
            fputcsv($file, ['School Report', 'Generated on ' . date('Y-m-d H:i')]);
            fputcsv($file, []);
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Sales', 'Total Orders', 'Average Order Value', 'Top Product']);
            fputcsv($file, [
                $reportData['summary']['total_sales'] ?? 0,
                $reportData['summary']['total_orders'] ?? 0,
                $reportData['summary']['average_order_value'] ?? 0,
                $reportData['summary']['top_product'] ?? 'N/A'
            ]);
            fputcsv($file, []);

            // Chart Data / Sales Detail
            fputcsv($file, ['Sales Detail']);
            fputcsv($file, ['Period / Date', 'Sales Amount']);

            if (isset($reportData['chart_data']['labels'])) {
                foreach ($reportData['chart_data']['labels'] as $index => $label) {
                    fputcsv($file, [
                        $label,
                        $reportData['chart_data']['sales'][$index] ?? 0
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function emailReport(Request $request)
    {
        // Check if school is authenticated
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to email reports.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $reportData = session('report_data', []);

         if (empty($reportData)) {
            return redirect()->back()->with('error', 'No report data found to email.');
        }

        // Simulation of Email Sending
        // In production: Mail::to($request->email)->send(new SchoolReportMail($reportData));
        \Illuminate\Support\Facades\Log::info('Sending School Report Email to: ' . $request->email);

        return redirect()->route('frontend.school.reports')
            ->with('success', 'Report email has been queued for sending to ' . $request->email . '.');
    }

    public function parentDashboard(Request $request)
    {
        // Get profiles from database
        $user = Auth::user();

        // Ensure user is a parent
        if ($user->isSchool()) {
            return redirect()->route('frontend.school.dashboard');
        }

        $profiles = $user->studentProfiles;

        // Get selected student ID from query parameter
        $selectedStudentId = $request->get('student_id');

        // If no student selected and profiles exist, select the first one
        if (!$selectedStudentId && $profiles->count() > 0) {
            $selectedStudentId = $profiles->first()->id;
        }

        // Find selected profile
        $selectedProfile = null;
        if ($selectedStudentId) {
            $selectedProfile = $profiles->firstWhere('id', (int)$selectedStudentId);
        }

        // Get purchased products for selected student from actual orders
        $purchasedProducts = [];
        if ($selectedProfile) {
            // Get all orders from session
            $orders = session('orders', []);

            // Load all products using the same logic as store page
            $folderProducts = $this->loadProductsFromFolder($selectedProfile->school_name);
            $additionalProducts = $this->getAdditionalProducts();
            $allProductsList = array_merge($folderProducts, $additionalProducts);

            // Create a map by product ID
            $allProducts = [];
            foreach ($allProductsList as $product) {
                $allProducts[$product['id']] = $product;
            }

            // Collect all products from orders for this student profile
            foreach ($orders as $order) {
                // Check if order belongs to this student (check items in order)
                foreach ($order['items'] as $item) {
                    if (isset($item['profile_id']) && (int)$item['profile_id'] === (int)$selectedProfile->id) {
                        // Get product details
                        if (isset($allProducts[$item['product_id']])) {
                            $product = $allProducts[$item['product_id']];
                            // Add to purchased products if not already added
                            $exists = false;
                            foreach ($purchasedProducts as $existingProduct) {
                                if ($existingProduct['id'] === $product['id']) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $purchasedProducts[] = array_merge($product, [
                                    'quantity' => $item['quantity'],
                                    'purchased_date' => $order['created_at'],
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Get school logo and address for selected profile
        $schoolLogo = null;
        $schoolAddress = null;
        if ($selectedProfile) {
            $school = \App\Models\Admin\Master\School::where('name', $selectedProfile->school_name)->first();
            if ($school) {
                $schoolLogo = $school->logo ? asset('storage/' . $school->logo) : null;
                $schoolAddress = $school->city;
            }
        }

        // Fetch active schools with their logos for the Add Student modal
        $schools = \App\Models\Admin\Master\School::where('status', 'active')
            ->select('id', 'name', 'city as location', 'logo')
            ->orderBy('name')
            ->get()
            ->map(function($school) {
                return [
                    'name' => $school->name,
                    'location' => $school->location ?? '',
                    'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
                ];
            });

        // Get parent phone number from session (in production, from database)
        $parentPhone = session('parent_phone', $user->phone ?? '+91 9159413234');

        return view('frontend.dashboard.parent-dashboard', compact('profiles', 'selectedProfile', 'purchasedProducts', 'schoolLogo', 'schoolAddress', 'parentPhone', 'schools'));
    }

    public function accountDetails()
    {
        $parentPhone = session('parent_phone', '+91 9159413234');
        return view('frontend.account.details', compact('parentPhone'));
    }

    public function profile()
    {
        $user = Auth::user();
        $savedAddresses = $user->addresses;
        return view('frontend.account.profile', compact('user', 'savedAddresses'));
    }

    public function updateProfileDetails(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|size:10|unique:users,phone,' . $user->id,
        ]);

        $user->name = $validated['name'] ?? $user->name;

        // Update email if provided and different
        if (!empty($validated['email']) && $validated['email'] !== $user->email) {
            $user->email = $validated['email'];
        }

        // Update phone if provided and different
        if (!empty($validated['phone']) && $validated['phone'] !== $user->phone) {
            $user->phone = $validated['phone'];
        }

        $user->save();

        // Update session data if needed
        if ($user->phone) {
            session(['parent_phone' => $user->phone]);
        }

        return redirect()->route('frontend.parent.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function myAddresses()
    {
        $user = Auth::user();
        $savedAddresses = $user->addresses;
        $parentPhone = session('parent_phone', $user->phone ?? '+91 9159413234');
        return view('frontend.account.addresses', compact('savedAddresses', 'parentPhone'));
    }

    public function saveAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',

                'alternative_number' => 'nullable|string|max:20',
                'block_name' => 'nullable|string|max:255',
                'address' => 'required|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'pincode' => 'required|string|max:10',
                'landmark' => 'nullable|string|max:255',
                'address_type' => 'required|string|in:home,office,others',
                'custom_address_type' => 'nullable|string|max:50',
            ]);

            $user = Auth::user();

            // Sync phone number to profile if empty (Use Case 1)
            if (empty($user->phone) && !empty($validated['phone'])) {
                $user->phone = $validated['phone'];
                $user->save();
                // Update session phone if needed
                session(['parent_phone' => $user->phone]);
            }

            $editingAddressId = $request->input('editing_address_index');

            $addressData = [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],

                'alternative_number' => $validated['alternative_number'] ?? '',
                'block_name' => $validated['block_name'] ?? '',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'pincode' => $validated['pincode'],
                'landmark' => $validated['landmark'] ?? '',
                'address_type' => $validated['address_type'],
                'address_type_display' => $validated['address_type'] === 'others' ? ($validated['custom_address_type'] ?? 'Other') : ucfirst($validated['address_type']),
            ];

            if ($editingAddressId) {
                // Update existing address
                $address = $user->addresses()->find($editingAddressId);
                if ($address) {
                    $address->update($addressData);
                    $message = 'Address updated successfully!';
                } else {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Address not found.']);
                    }
                    return redirect()->back()->with('error', 'Address not found.');
                }
            } else {
                // Create new address
                $user->addresses()->create($addressData);
                $message = 'Address saved successfully!';
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error saving address: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Error saving address: ' . $e->getMessage());
        }
    }

    public function deleteAddress($addressId)
    {
        $user = Auth::user();
        $address = $user->addresses()->find($addressId);

        if ($address) {
            $address->delete();
            return redirect()->back()->with('success', 'Address deleted successfully!');
        }

        return redirect()->back()->with('error', 'Address not found.');
    }

    public function createProfile()
    {
        // Fetch active schools with their logos
        $schools = \App\Models\Admin\Master\School::where('status', 'active')
            ->select('id', 'name', 'city as location', 'logo')
            ->orderBy('name')
            ->get()
            ->map(function($school) {
                return [
                    'name' => $school->name,
                    'location' => $school->location ?? '',
                    'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
                ];
            });

        return view('frontend.dashboard.create-profile', compact('schools'));
    }

    public function storeProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_name' => 'required|string|max:255',
                'school_name' => 'required|string|max:255',
                'grade' => 'required|string',
                'section' => 'required|string|max:10',
                'gender' => 'required|in:male,female',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the highlighted fields and try again.',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }

        $user = Auth::user();
        $profile = $user->studentProfiles()->create([
            'student_name' => $validated['student_name'],
            'school_name' => $validated['school_name'],
            'grade' => $validated['grade'],
            'section' => $validated['section'],
            'gender' => $validated['gender'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student profile created successfully!',
                'profile_id' => $profile->id,
            ]);
        }

        return redirect()->route('frontend.parent.dashboard', ['student_id' => $profile->id])
            ->with('success', 'Student profile created successfully!');
    }

    public function editProfile($profileId)
    {
        $user = Auth::user();
        $profile = $user->studentProfiles()->find($profileId);

        if (!$profile) {
            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Profile not found.');
        }

        return view('frontend.dashboard.create-profile', compact('profile'));
    }

    public function updateProfile(Request $request, $profileId)
    {
        try {
            $validated = $request->validate([
                'student_name' => 'required|string|max:255',
                'school_name' => 'required|string|max:255',
                'grade' => 'required|string',
                'section' => 'required|string|max:10',
                'gender' => 'required|in:male,female',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the highlighted fields and try again.',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }

        $user = Auth::user();
        $profile = $user->studentProfiles()->find($profileId);

        if (!$profile) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found.',
                ], 404);
            }

            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Profile not found.');
        }

        $profile->update([
            'student_name' => $validated['student_name'],
            'school_name' => $validated['school_name'],
            'grade' => $validated['grade'],
            'section' => $validated['section'],
            'gender' => $validated['gender'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student profile updated successfully!',
                'profile_id' => $profile->id,
            ]);
        }

        return redirect()->route('frontend.parent.dashboard', ['student_id' => $profileId])
            ->with('success', 'Student profile updated successfully!');
    }

    public function deleteProfile($profileId)
    {
        $user = Auth::user();
        $profile = $user->studentProfiles()->find($profileId);

        if ($profile) {
            $profile->delete();
        }

        // Clear cart items for this profile from database
        \App\Models\Cart::where('user_id', $user->id)
            ->where('profile_id', $profileId)
            ->delete();

        return redirect()->route('frontend.parent.dashboard')
            ->with('success', 'Student profile deleted successfully!');
    }

    /**
     * Get product images from the 8 available images in product_images folder
     * Returns at least 5 images for each product
     */
    private function getProductImages($productId)
    {
        // Available images from product_images folder
        $availableImages = [
            asset('assets/img/product_images/Image1.png'),
            asset('assets/img/product_images/Image2.png'),
            asset('assets/img/product_images/Image3.png'),
            asset('assets/img/product_images/Image4.png'),
            asset('assets/img/product_images/Image5.png'),
            asset('assets/img/product_images/Image6.png'),
            asset('assets/img/product_images/Image7.png'),
            asset('assets/img/product_images/Image8.png'),
        ];

        // Use product ID to create a consistent but varied distribution
        // This ensures each product gets a different combination
        $startIndex = ($productId - 1) % count($availableImages);
        $images = [];

        // Get at least 5 images, cycling through the available images
        for ($i = 0; $i < 5; $i++) {
            $index = ($startIndex + $i) % count($availableImages);
            $images[] = $availableImages[$index];
        }

        // Add more images if needed (up to 8 total)
        if (count($images) < 8) {
            $remaining = 8 - count($images);
            for ($i = 0; $i < $remaining; $i++) {
                $index = ($startIndex + 5 + $i) % count($availableImages);
                if (!in_array($availableImages[$index], $images)) {
                    $images[] = $availableImages[$index];
                }
            }
        }

        return $images;
    }

    /**
     * Load products from folder based on school name
     */
    private function loadProductsFromFolder($schoolName)
    {
        $products = [];
        $productId = 1;
        $basePath = public_path('assets/img/product');

        // Map school names to folder names
        $schoolFolderMap = [
            'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur' => 'Bharatiya_Vidhya',
            'Stanes ICSE School' => 'Stanes ICSE School',
        ];

        $folderName = $schoolFolderMap[$schoolName] ?? null;

        if (!$folderName || !File::exists($basePath . '/' . $folderName)) {
            return $products;
        }

        $folderPath = $basePath . '/' . $folderName;
        $items = File::allFiles($folderPath);

        foreach ($items as $item) {
            // Skip if it's inside a subfolder (we'll handle subfolders separately)
            $relativePath = str_replace($folderPath . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);

            // If it's a direct file (not in subfolder)
            if (count($pathParts) === 1 && in_array(strtolower($item->getExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
                $fileName = $item->getFilename();
                $productName = str_replace(['.jpg', '.jpeg', '.png', '.gif'], '', $fileName);
                $productName = str_replace(['_', '-'], ' ', $productName);

                // Determine category based on product name
                $category = 'regular_uniforms';
                $nameLower = strtolower($productName);
                if (strpos($nameLower, 'fabric') !== false) {
                    $category = 'fabrics';
                } elseif (strpos($nameLower, 'sport') !== false) {
                    $category = 'sports';
                }

                $currentProductId = $productId++;
                $productImages = $this->getProductImages($currentProductId);

                $products[] = [
                    'id' => $currentProductId,
                    'name' => $productName,
                    'price' => rand(300, 800), // Default price, should come from database
                    'image' => $productImages[0], // First image as primary
                    'images' => $productImages, // At least 5 images
                    'description' => 'Premium quality ' . strtolower($productName),
                    'type' => 'authorized',
                    'category' => $category,
                    'sizes' => ['24', '26', '28', '30', '32'],
                ];
            }
        }

        // Handle subfolders (for products with multiple images like Stanes ICSE)
        $directories = File::directories($folderPath);
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            $files = File::files($directory);

            if (count($files) > 0) {
                // Get the first image as primary
                $firstFile = $files[0];
                $productName = str_replace(['_', '-'], ' ', $dirName);

                // Collect all images
                $images = [];
                foreach ($files as $file) {
                    if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $images[] = asset('assets/img/product/' . $folderName . '/' . $dirName . '/' . $file->getFilename());
                    }
                }

                // Determine category based on product name
                $category = 'regular_uniforms';
                $nameLower = strtolower($productName);
                if (strpos($nameLower, 'fabric') !== false) {
                    $category = 'fabrics';
                } elseif (strpos($nameLower, 'sport') !== false) {
                    $category = 'sports';
                }

                $currentProductId = $productId++;
                $productImages = $this->getProductImages($currentProductId);

                $products[] = [
                    'id' => $currentProductId,
                    'name' => $productName,
                    'price' => rand(300, 800), // Default price, should come from database
                    'image' => $productImages[0], // First image as primary
                    'images' => $productImages, // At least 5 images
                    'description' => 'Premium quality ' . strtolower($productName),
                    'type' => 'authorized',
                    'category' => $category,
                    'sizes' => ['24', '26', '28', '30', '32'],
                ];
            }
        }

        return $products;
    }

    /**
     * Get additional products for all categories
     */
    private function getAdditionalProducts()
    {
        // Get the 8 product images
        $productImages = [
            asset('assets/img/product_images/Image1.png'),
            asset('assets/img/product_images/Image2.png'),
            asset('assets/img/product_images/Image3.png'),
            asset('assets/img/product_images/Image4.png'),
            asset('assets/img/product_images/Image5.png'),
            asset('assets/img/product_images/Image6.png'),
            asset('assets/img/product_images/Image7.png'),
            asset('assets/img/product_images/Image8.png'),
        ];

        return [
            // Optional Products
            [
                'id' => 100,
                'name' => 'School Blazer',
                'price' => 1200,
                'image' => $productImages[0],
                'images' => array_slice($productImages, 0, 5),
                'description' => 'Premium school blazer with school emblem',
                'type' => 'optional',
                'category' => 'regular_uniforms',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
            ],
            [
                'id' => 101,
                'name' => 'School Sweater',
                'price' => 650,
                'image' => $productImages[1],
                'images' => array_slice($productImages, 1, 5),
                'description' => 'Warm and comfortable school sweater',
                'type' => 'optional',
                'category' => 'regular_uniforms',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
            ],
            [
                'id' => 102,
                'name' => 'School Cap',
                'price' => 200,
                'image' => $productImages[2],
                'images' => array_slice($productImages, 2, 5),
                'description' => 'School cap with logo',
                'type' => 'optional',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],

            // Merchandised Products
            [
                'id' => 200,
                'name' => 'School Bag',
                'price' => 800,
                'image' => $productImages[3],
                'images' => array_slice($productImages, 3, 5),
                'description' => 'Durable school bag with compartments',
                'type' => 'merchandised',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 201,
                'name' => 'School Badge Set',
                'price' => 150,
                'image' => $productImages[4],
                'images' => array_slice($productImages, 4, 5),
                'description' => 'Complete set of school badges',
                'type' => 'merchandised',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 202,
                'name' => 'School ID Card Holder',
                'price' => 100,
                'image' => $productImages[5],
                'images' => array_slice($productImages, 5, 5),
                'description' => 'Protective ID card holder with lanyard',
                'type' => 'merchandised',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],

            // Back to School Products (Stationary)
            [
                'id' => 300,
                'name' => 'School Belt',
                'price' => 300,
                'image' => $productImages[6],
                'images' => array_slice($productImages, 6, 5),
                'description' => 'Leather school belt with adjustable buckle',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['24', '26', '28', '30', '32'],
            ],
            [
                'id' => 301,
                'name' => 'Geometry Box',
                'price' => 250,
                'image' => $productImages[7],
                'images' => array_slice($productImages, 7, 5),
                'description' => 'Complete geometry box with compass, protractor, and ruler',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 302,
                'name' => 'Pen Set',
                'price' => 120,
                'image' => $productImages[0],
                'images' => array_slice($productImages, 0, 5),
                'description' => 'Set of 5 premium pens (blue, black, red, green, pencil)',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 303,
                'name' => 'Pencil Set',
                'price' => 80,
                'image' => $productImages[1],
                'images' => array_slice($productImages, 1, 5),
                'description' => 'Set of 10 HB pencils',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 304,
                'name' => 'Water Bottle',
                'price' => 350,
                'image' => $productImages[2],
                'images' => array_slice($productImages, 2, 5),
                'description' => 'Insulated water bottle with school logo',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['22', '24', '26', '28', '30', '32'],
            ],
            [
                'id' => 305,
                'name' => 'Notebook Set',
                'price' => 400,
                'image' => $productImages[3],
                'images' => array_slice($productImages, 3, 5),
                'description' => 'Set of 5 ruled notebooks (200 pages each)',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 306,
                'name' => 'Eraser Set',
                'price' => 50,
                'image' => $productImages[4],
                'images' => array_slice($productImages, 4, 5),
                'description' => 'Set of 5 high-quality erasers',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 307,
                'name' => 'Sharpener',
                'price' => 40,
                'image' => asset('assets/img/product/product1-14.png'),
                'images' => [asset('assets/img/product/product1-14.png')],
                'description' => 'Metal pencil sharpener with container',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
            [
                'id' => 308,
                'name' => 'Scale Set',
                'price' => 100,
                'image' => asset('assets/img/product/product1-15.png'),
                'images' => [asset('assets/img/product/product1-15.png')],
                'description' => 'Set of 3 rulers (15cm, 30cm, 45cm)',
                'type' => 'back_to_school',
                'category' => 'regular_uniforms',
                'sizes' => ['One Size'],
            ],
        ];
    }

    public function store(Request $request)
    {
        // Get profiles from database (same as dashboard)
        $user = Auth::user();
        $profiles = $user->studentProfiles;

        // Get selected student ID from query parameter
        $profileId = $request->get('profile_id');

        // If no profile_id provided and profiles exist, auto-select the first one
        if (!$profileId && $profiles->count() > 0) {
            $profileId = $profiles->first()->id;
        }

        // Find the selected profile
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = $profiles->firstWhere('id', (int)$profileId);
        }

        if (!$selectedProfile) {
            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Please select a student profile first.');
        }

        // Fetch real products from database
        $schoolName = $selectedProfile->school_name;
        $school = \App\Models\Admin\Master\School::where('name', $schoolName)->first();

        $allProducts = [];

        if ($school) {
            $dbProductsQuery = \App\Models\Admin\Master\ProductMapping::with('variants')->where('school_id', $school->id)
                ->where('status', 'live'); // Assuming 'live' is the active status

            // Filter by grade if available in profile
            if ($selectedProfile->grade) {
                $rawGrade = $selectedProfile->grade;
                $gradeVariants = [$rawGrade];

                // Simple normalization helper for Roman/Arabic
                $romanMap = [
                    '1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V',
                    '6' => 'VI', '7' => 'VII', '8' => 'VIII', '9' => 'IX', '10' => 'X',
                    '11' => 'XI', '12' => 'XII'
                ];
                $arabicMap = array_flip($romanMap);

                // Add alternatives
                if (isset($romanMap[$rawGrade])) {
                    $gradeVariants[] = $romanMap[$rawGrade];
                }
                if (isset($arabicMap[strtoupper($rawGrade)])) {
                    $gradeVariants[] = $arabicMap[strtoupper($rawGrade)];
                }

                // Add partial matches like "Class 12" or "Grade XII" if not already covered
                // (Note: This might be overkill if data specific, but robust)
                $extras = [];
                foreach ($gradeVariants as $v) {
                    $extras[] = "Class $v";
                    $extras[] = "Grade $v";
                }
                $gradeVariants = array_merge($gradeVariants, $extras);


                $dbProductsQuery->where(function($q) use ($gradeVariants) {
                    $q->whereIn('grade', $gradeVariants)
                      ->orWhereNull('grade')
                      ->orWhere('grade', '');
                });
            }

            // Filter by gender if available in profile
            if ($selectedProfile->gender) {
                $gender = strtolower($selectedProfile->gender);
                // Map legacy terms if necessary, though profile should ideally be 'male'/'female'
                $genderMap = [
                    'boys' => 'male',
                    'girls' => 'female',
                    'male' => 'male',
                    'female' => 'female'
                ];
                $mappedGender = $genderMap[$gender] ?? $selectedProfile->gender;

                $dbProductsQuery->where(function($q) use ($mappedGender) {
                    $q->where('gender', $mappedGender)
                      ->orWhere('gender', 'unisex')
                      ->orWhere('gender', 'Unisex');
                });
            }

            $dbProducts = $dbProductsQuery->get();

            foreach ($dbProducts as $dbProduct) {
                // Determine image URL
                $image = $dbProduct->featured_image
                    ? (\Illuminate\Support\Str::startsWith($dbProduct->featured_image, 'http') ? $dbProduct->featured_image : asset('storage/' . $dbProduct->featured_image))
                    : asset('assets/img/product/product1-1.png');

                // Handle media images if available
                $images = [$image];
                if ($dbProduct->media_images) {
                    foreach ($dbProduct->media_images as $mediaImg) {
                        $images[] = \Illuminate\Support\Str::startsWith($mediaImg, 'http') ? $mediaImg : asset('storage/' . $mediaImg);
                    }
                }

                // Determine sizes
                $sizes = ['S', 'M', 'L', 'XL', 'XXL']; // Fallback
                if ($dbProduct->variants && $dbProduct->variants->count() > 0) {
                    $sizes = $dbProduct->variants->pluck('option')->toArray();
                }

                $allProducts[] = [
                    'id' => $dbProduct->id,
                    'name' => $dbProduct->product_name,
                    'price' => $dbProduct->price_regular,
                    'original_price' => $dbProduct->price_sale, // Assuming price_sale is the original/higher price if on sale, or vice versa. Adjust logic as needed.
                    'image' => $image,
                    'images' => $images,
                    'description' => $dbProduct->description,
                    'type' => strtolower($dbProduct->product_type ?? 'authorized'),
                    'category' => $dbProduct->category ?? 'regular_uniforms',
                    'gender' => match(strtolower($dbProduct->gender ?? 'unisex')) {
                        'male' => 'Male',
                        'female' => 'Female',
                        default => 'Unisex'
                    },
                    'sizes' => $sizes,
                    'tags' => $dbProduct->tag_name ? explode(',', $dbProduct->tag_name) : [],
                    'sku' => $dbProduct->id, // Use ID as SKU for now
                ];
            }
        } else {
            // Fallback or empty if school not found in DB
            // You might want to keep the fake data here for testing if DB is empty
            // For now, let's return empty to prove the connection (or lack thereof)
             $allProducts = [];
        }

        return view('frontend.store.index', compact('selectedProfile', 'allProducts'));
    }

    public function productDetail($productId, Request $request)
    {
        // Get profiles from database (same as store method)
        $user = Auth::user();
        $profiles = $user->studentProfiles;

        $profileId = $request->get('profile_id');

        // If no profile_id provided and profiles exist, auto-select the first one
        if (!$profileId && $profiles->count() > 0) {
            $profileId = $profiles->first()->id;
        }

        // Find the selected profile
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = $profiles->firstWhere('id', (int)$profileId);
        }

        if (!$selectedProfile) {
            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Please select a student profile first.');
        }

        // Fetch real product from database
        $dbProduct = \App\Models\Admin\Master\ProductMapping::with('variants')->find($productId);

        if (!$dbProduct) {
            return redirect()->route('frontend.parent.store', ['profile_id' => $profileId])
                ->with('error', 'Product not found.');
        }

        // Determine image URL
        $image = $dbProduct->featured_image
            ? (\Illuminate\Support\Str::startsWith($dbProduct->featured_image, 'http') ? $dbProduct->featured_image : asset('storage/' . $dbProduct->featured_image))
            : asset('assets/img/product/product1-1.png');

        // Handle media images if available
        $images = [$image];
        if ($dbProduct->media_images) {
            foreach ($dbProduct->media_images as $mediaImg) {
                $images[] = \Illuminate\Support\Str::startsWith($mediaImg, 'http') ? $mediaImg : asset('storage/' . $mediaImg);
            }
        }

        // Determine sizes from variants or fallback
        $sizes = ['S', 'M', 'L', 'XL', 'XXL']; // Default
        if ($dbProduct->variants && $dbProduct->variants->count() > 0) {
            $sizes = $dbProduct->variants->pluck('option')->toArray();
        }

        $product = [
            'id' => $dbProduct->id,
            'name' => $dbProduct->product_name,
            'price' => $dbProduct->price_regular,
            'original_price' => $dbProduct->price_sale,
            'image' => $image,
            'images' => $images,
            'description' => $dbProduct->description,
            'type' => $dbProduct->product_type ?? 'authorized',
            'category' => $dbProduct->category ?? 'regular_uniforms',
            'sizes' => $sizes,
            'size_chart_path' => $dbProduct->size_chart_path,
            'video_url' => $dbProduct->video_url,
            'tags' => $dbProduct->tag_name ? explode(',', $dbProduct->tag_name) : [],
            'sku' => $dbProduct->id,
            'variants' => $dbProduct->variants, // Pass full variants for stock checking if needed later
        ];

    return view('frontend.store.product-detail', compact('selectedProfile', 'product'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'profile_id' => 'required|integer',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        // Validate Stock
        $product = \App\Models\Admin\Master\ProductMapping::with('variants')->find($request->product_id);
        if ($product) {
            if ($product->variants->count() > 0) {
                // Check variant stock
                $variant = $product->variants->where('option', $request->size)->first();
                if (!$variant) {
                    return back()->with('error', 'Invalid size selected.');
                }
                if ($variant->stock < $request->quantity) {
                    return back()->with('error', 'Selected size is out of stock.');
                }
            } else {
                // Check main stock
                if ($product->inventory_stock < $request->quantity) {
                    return back()->with('error', 'Product is out of stock.');
                }
            }
        }

        // Check if the same product with same size and profile already exists in cart
        // Note: Unique constraint exists on [user, profile, product, size], so we must update if exists
        $existingItem = \App\Models\Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('profile_id', $request->profile_id)
            ->where('size', $request->size)
            ->first();

        if ($existingItem) {
             // Check if adding more exceeds stock
             $newQuantity = $existingItem->quantity + $request->quantity;
             if ($product && $product->variants->count() > 0) {
                 $variant = $product->variants->where('option', $request->size)->first();
                 if ($variant && $variant->stock < $newQuantity) {
                     return back()->with('error', 'Cannot add more items. Exceeds available stock.');
                 }
             } elseif ($product && $product->inventory_stock < $newQuantity) {
                 return back()->with('error', 'Cannot add more items. Exceeds available stock.');
             }

            // Update quantity of existing item
            $existingItem->increment('quantity', $request->quantity);
        } else {
            // Add new item to cart
            \App\Models\Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'profile_id' => $request->profile_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
            ]);
        }

        // Redirect to cart page to show all items
        return redirect()->route('frontend.parent.cart')
            ->with('success', 'Product added to cart successfully!');
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'profile_id' => 'required|integer',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        // Check if item with same product, profile, and size already exists
        $existingItem = \App\Models\Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('profile_id', $request->profile_id)
            ->where('size', $request->size)
            ->first();

        if ($existingItem) {
            // Increase quantity
            $existingItem->increment('quantity', $request->quantity);
        } else {
            // Add new item
            \App\Models\Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'profile_id' => $request->profile_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
            ]);
        }

        // Redirect to cart page (not checkout) - user can review and then proceed to checkout
        return redirect()->route('frontend.parent.cart')
            ->with('success', 'Product added to cart! Review your order and proceed to checkout.');
    }

    public function cart(Request $request)
    {
        $user = Auth::user();
        $profiles = $user->studentProfiles;

        // Get cart items from database
        $cartDbItems = \App\Models\Cart::where('user_id', $user->id)->get();

        // Get all unique product IDs from cart
        $productIds = $cartDbItems->pluck('product_id')->unique()->toArray();

        // Fetch products from DB
        $products = \App\Models\Admin\Master\ProductMapping::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $total = 0;

        foreach ($cartDbItems as $item) {
            if (isset($products[$item->product_id])) {
                $product = $products[$item->product_id];
                $itemTotal = $product->price_sale * $item->quantity;
                $total += $itemTotal;

                // Find student name from database profiles
                $studentName = 'Unknown Student';
                $profile = $profiles->firstWhere('id', (int)$item->profile_id);
                if ($profile) {
                    $studentName = $profile->student_name;
                }

                // Get product image - try multiple fields
                $productImage = null;
                if ($product->featured_image) {
                    $productImage = $product->featured_image;
                } elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                    $productImage = $product->media_images[0];
                } elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                    $productImage = $product->media_gallery[0];
                }

                $cartItems[] = [
                    'id' => $item->id, // Cart item ID for removal
                    'product_id' => $item->product_id,
                    'profile_id' => $item->profile_id,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'name' => $product->product_name,
                    'price' => $product->price_sale,
                    'image' => $productImage ? asset('storage/' . $productImage) : null,
                    'item_total' => $itemTotal,
                    'student_name' => $studentName,
                ];
            }
        }

        // For the view's "Buy More" link
        $profileId = $request->get('profile_id', $cartDbItems->first()?->profile_id);
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = $profiles->firstWhere('id', (int)$profileId);
        }

        return view('frontend.cart.index', compact('cartItems', 'total', 'profiles', 'selectedProfile'));
    }

    public function removeFromCart(Request $request)
    {
        $cartItemId = $request->get('id') ?? $request->get('index');
        $user = Auth::user();

        // Delete cart item by ID
        \App\Models\Cart::where('id', $cartItemId)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('frontend.parent.cart')
            ->with('success', 'Item removed from cart.');
    }

    public function checkoutPage(Request $request)
    {
        $user = Auth::user();
        $profiles = $user->studentProfiles;

        // Get cart items from database
        $cartDbItems = \App\Models\Cart::where('user_id', $user->id)->get();

        if ($cartDbItems->isEmpty()) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Get selected items from request (comma-separated IDs)
        $selectedItemsStr = $request->get('selected_items', '');
        $selectedIds = [];
        if (!empty($selectedItemsStr)) {
            $selectedIds = array_map('intval', explode(',', $selectedItemsStr));
            $filteredCartItems = $cartDbItems->whereIn('id', $selectedIds);
        } else {
            // If no selection, use all items
            $filteredCartItems = $cartDbItems;
        }

        if ($filteredCartItems->isEmpty()) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Please select at least one item to checkout.');
        }

        // Get profile_id from cart items
        $profileId = $filteredCartItems->first()?->profile_id;

        // Find the selected profile from database
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = $profiles->firstWhere('id', (int)$profileId);
        }

        // Get unique product IDs from filtered cart
        $productIds = $filteredCartItems->pluck('product_id')->unique()->toArray();

        // Fetch products from DB
        $products = \App\Models\Admin\Master\ProductMapping::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $subtotal = 0;
        $totalTax = 0;

        foreach ($filteredCartItems as $item) {
            if (isset($products[$item->product_id])) {
                $product = $products[$item->product_id];
                $itemSubtotal = $product->price_sale * $item->quantity;

                // Calculate tax for this item using price_tax field (which stores the percentage)
                $taxPercentage = $product->price_tax ?? 0;
                $itemTax = ($itemSubtotal * $taxPercentage) / 100;
                $itemTotal = $itemSubtotal + $itemTax;

                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;

                // Get student name from profile
                $studentName = 'Unknown Student';
                $profile = $profiles->firstWhere('id', (int)$item->profile_id);
                if ($profile) {
                    $studentName = $profile->student_name;
                }

                // Get product image - try multiple fields
                $productImage = null;
                if ($product->featured_image) {
                    $productImage = $product->featured_image;
                } elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                    $productImage = $product->media_images[0];
                } elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                    $productImage = $product->media_gallery[0];
                }

                $cartItems[] = [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'profile_id' => $item->profile_id,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'name' => $product->product_name,
                    'price' => $product->price_sale,
                    'image' => $productImage ? asset('storage/' . $productImage) : null,
                    'item_total' => $itemTotal,
                    'item_subtotal' => $itemSubtotal,
                    'item_tax' => $itemTax,
                    'tax_percentage' => $taxPercentage,
                    'student_name' => $studentName,
                ];
            }
        }

        $total = $subtotal + $totalTax;

        // Get saved addresses from database instead of session
        $savedAddresses = $user->addresses;

        // Store selected IDs in session for processCheckout
        $selectedIds = $filteredCartItems->pluck('id')->toArray();
        session(['checkout_selected_cart_ids' => $selectedIds]);

        // Check active payment gateways
        $razorpayGateway = \App\Models\Admin\Master\PaymentGateway::where('provider', 'razorpay')
            ->where('is_active', true)
            ->first();
        // Enable if in DB OR if env has key (fallback)
        $razorpayEnabled = $razorpayGateway ? true : (!empty(env('RAZORPAY_KEY')));

        return view('frontend.checkout.index', compact('cartItems', 'total', 'subtotal', 'totalTax', 'profiles', 'selectedProfile', 'savedAddresses', 'razorpayEnabled'));
    }

    public function processCheckout(Request $request)
    {
        $user = Auth::user();

        // Get cart items from database
        $cartDbItems = \App\Models\Cart::where('user_id', $user->id)->get();

        if ($cartDbItems->isEmpty()) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Get selected items from session (set during checkoutPage)
        $selectedCartIds = session('checkout_selected_cart_ids', []);

        // Filter cart to only include selected items
        if (!empty($selectedCartIds)) {
            $filteredCartItems = $cartDbItems->whereIn('id', $selectedCartIds);
        } else {
            // If no selection was made, use all items
            $filteredCartItems = $cartDbItems;
        }

        if ($filteredCartItems->isEmpty()) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Please select at least one item to checkout.');
        }

        // Get addresses from database instead of session
        $savedAddresses = $user->addresses;

        if ($savedAddresses->count() === 0 && !$request->has('name')) {
            return redirect()->route('frontend.parent.checkout')
                ->with('error', 'Please add a shipping address before placing your order.');
        }

        try {
            // Save address if it's a new one
            $selectedAddressIndex = $request->get('selected_address');

            $shippingAddress = [];
            if ($selectedAddressIndex !== null && $savedAddresses->has($selectedAddressIndex)) {
                // Use saved address from database
                $address = $savedAddresses->get($selectedAddressIndex);
                $shippingAddress = [
                    'name' => $address->name,

                    'phone' => $address->phone,
                    'address' => $address->address,
                    'city' => $address->city,
                    'state' => $address->state,
                    'pincode' => $address->pincode,
                    'address_type' => $address->address_type,
                    'address_type_display' => $address->address_type_display,
                ];
            } else {
                // Validate new address
                $request->validate([
                    'name' => 'required|string|max:255',

                    'phone' => 'required|string|max:20',
                    'address' => 'required|string',
                    'city' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'pincode' => 'required|string|max:10',
                ]);

                // Determine the display name for address type
                $addressTypeDisplay = $request->address_type ?? 'home';
                if ($request->address_type === 'others' && $request->custom_address_type) {
                    $addressTypeDisplay = $request->custom_address_type;
                }

                // Use new address and save it
                $shippingAddress = [
                    'name' => $request->name,

                    'phone' => $request->phone,
                    'alternative_number' => $request->alternative_number ?? '',
                    'block_name' => $request->block_name ?? '',
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                    'landmark' => $request->landmark ?? '',
                    'address_type' => $request->address_type ?? 'home',
                    'address_type_display' => $addressTypeDisplay, // Store the display name
                ];

                // Save to database
                $user->addresses()->create($shippingAddress);
            }

            if (empty($shippingAddress)) {
                return redirect()->route('frontend.parent.checkout')
                    ->with('error', 'Please add or select a shipping address before placing your order.');
            }

            // Allow multiple students in one order

            // Create order with unique ID
            // Get the last order id to increment
            $lastOrder = \App\Models\Admin\Master\Order::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderNumber = 'SOCO-' . $nextId;

            // Get profiles from database
            $profiles = $user->studentProfiles;

            // DB Transaction to ensure data consistency
            \Illuminate\Support\Facades\DB::beginTransaction();

            $processedCartIds = [];
            foreach ($filteredCartItems as $index => $item) {
                // Fetch product details from DB
                $product = \App\Models\Admin\Master\ProductMapping::find($item->product_id);

                if (!$product) {
                    continue; // Skip if product not found
                }

                // Find student profile for this item
                $studentProfile = $profiles->firstWhere('id', (int)$item->profile_id);

                // Get school_id - lookup school by name from student profile
                $schoolId = null;
                if ($studentProfile && $studentProfile->school_name) {
                    $school = \App\Models\Admin\Master\School::where('name', $studentProfile->school_name)->first();
                    $schoolId = $school ? $school->id : null;
                }
                // Fallback to product's school_id if student profile doesn't have school
                if (!$schoolId && $product->school_id) {
                    $schoolId = $product->school_id;
                }

                // Create Order Record with unique suffix for each item
                // Format: SOCO-[ID]-[Index]
                $uniqueOrderNumber = $orderNumber . '-' . ($index + 1);

                // Calculate tax
                $taxPercentage = $product->price_tax ?? 0;
                $itemSubtotal = $product->price_sale * $item->quantity;
                $itemTax = ($itemSubtotal * $taxPercentage) / 100;
                $itemTotal = $itemSubtotal + $itemTax;

                $order = \App\Models\Admin\Master\Order::create([
                    'order_number' => $uniqueOrderNumber,
                    'school_id' => $schoolId,
                    'order_date' => now(),
                    'student_name' => $studentProfile ? $studentProfile->student_name : 'Unknown',
                    'grade' => $studentProfile ? $studentProfile->grade : $product->grade,
                    'category' => $product->category ?? 'General',
                    'item_name' => $product->product_name,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'customer_name' => $shippingAddress['name'],
                    'customer_address' => $shippingAddress['address'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['state'] . ' - ' . $shippingAddress['pincode'],
                    'customer_phone' => $shippingAddress['phone'],
                    'customer_email' => $shippingAddress['email'] ?? Auth::user()->email ?? '',
                    'total_amount' => $itemTotal,
                    'tax_amount' => $itemTax,
                    'shipping_cost' => 0,
                    'payment_status' => session('payment_method') === 'razorpay' ? 'paid' : 'pending',
                    'order_status' => 'processing',
                    'payment_method' => session('payment_method'),
                    'payment_id' => session('payment_id'),
                    'amount_paid' => session('payment_method') === 'razorpay' ? $itemTotal : 0,
                    'payment_details' => session('payment_details'),
                ]);

                // Create Payment Record
                if (session('payment_method') === 'razorpay' && session('payment_id')) {
                    \App\Models\Payment::create([
                        'order_id' => $order->id,
                        'payment_id' => session('payment_id'),
                        'total_amount' => $itemTotal,
                        'tax_amount' => $itemTax,
                        'shipping_cost' => 0,
                        'amount_paid' => $itemTotal,
                        'payment_status' => 'paid',
                        'payment_method' => session('payment_method'),
                        'payment_type' => 'online', // Or derive from razorpay method if available
                        'payment_details' => session('payment_details'),
                    ]);
                }

                // Decrement Inventory
                if ($product->variants()->exists()) {
                    $variant = $product->variants()->where('option', $item->size)->first();
                    if ($variant) {
                        $variant->decrement('stock', $item->quantity);
                        $product->updateTotalStock(); // Recalculate total
                    }
                } else {
                    $product->decrement('inventory_stock', $item->quantity);
                }

                // Track processed cart item IDs
                $processedCartIds[] = $item->id;
            }

            \Illuminate\Support\Facades\DB::commit();

            // Clear processed cart items from database
            \App\Models\Cart::whereIn('id', $processedCartIds)->delete();

            // Clear checkout session data
            session()->forget(['checkout_selected_cart_ids', 'orders', 'payment_method', 'payment_id', 'amount_paid', 'payment_details']);

            // Create Notification for Master Admins
            \App\Models\Notification::create([
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "Order #{$orderNumber} placed by {$shippingAddress['name']}",
                'target_role' => 'master',
                'data' => ['order_number' => $orderNumber],
            ]);
            // Create Notification for Inventory Admins
            \App\Models\Notification::create([
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "Order #{$orderNumber} placed by {$shippingAddress['name']}",
                'target_role' => 'inventory',
                'data' => ['order_number' => $orderNumber],
            ]);

            return redirect()->route('frontend.parent.orders')->with('success', 'Order placed successfully! Order # ' . $orderNumber);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please correct the highlighted errors and try again.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Checkout processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('frontend.parent.checkout')
                ->with('error', 'An unexpected error occurred while processing your order. Please try again: ' . $e->getMessage());
        }
    }


    public function orders()
    {
        // Get profiles from database instead of session
        $user = Auth::user();
        $profiles = $user->studentProfiles;
        $studentNames = $profiles->pluck('student_name')->toArray();

        // Fetch orders from DB where student_name matches any of the profiles
        $orders = \App\Models\Admin\Master\Order::whereIn('student_name', $studentNames)
            ->orderByDesc('created_at')
            ->get();

        // Group orders by the base order number (SOCO-ID-Index -> ID)
        $groupedOrders = $orders->groupBy(function ($order) {
            $parts = explode('-', $order->order_number);
            return isset($parts[1]) ? $parts[1] : $parts[0];
        });

        // Transform for view
        $formattedOrders = [];
        foreach ($groupedOrders as $baseOrderNumber => $items) {
            $firstItem = $items->first();

            // Check if there's a return/exchange request for any item in this order
            $orderIds = $items->pluck('id')->toArray();
            $returnRequests = \App\Models\Admin\Master\ReturnExchangeRequest::whereIn('order_id', $orderIds)
                ->get()
                ->keyBy('order_id');



            $formattedOrders[] = [
                'id' => $baseOrderNumber,
                'status' => $firstItem->order_status,
                'created_at' => $firstItem->created_at,
                'total' => $items->sum('total_amount'),
                'items' => $items->map(function ($item) use ($returnRequests) {
                    // Get product image - try strict match first, then loose match
                    $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)
                        ->where('school_id', $item->school_id)
                        ->first();

                    if (!$product) {
                        // Fallback: try matching just by name
                        $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)->first();
                    }

                    $productImage = null;
                    if ($product) {
                        if ($product->featured_image) {
                            $productImage = $product->featured_image;
                        } elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                            $productImage = $product->media_images[0];
                        } elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                            $productImage = $product->media_gallery[0];
                        }
                    }

                    return [
                        'id' => $item->id,
                        'product_id' => $product ? $product->id : null,
                        'name' => $item->item_name,
                        'price' => $item->total_amount / ($item->quantity > 0 ? $item->quantity : 1),
                        'quantity' => $item->quantity,
                        'size' => $item->size,
                        'image' => $productImage ? asset('storage/' . $productImage) : null,
                        'return_request' => $returnRequests[$item->id] ?? null,
                    ];
                })->toArray(),
            ];
        }

        return view('frontend.orders.index', ['orders' => $formattedOrders, 'profiles' => $profiles]);
    }

    public function trackOrder($orderId)
    {
        // Get orders from database based on the base order number
        $user = Auth::user();
        $profiles = $user->studentProfiles;
        $studentNames = $profiles->pluck('student_name')->toArray();

        $orders = \App\Models\Admin\Master\Order::whereIn('student_name', $studentNames)
            ->where(function($q) use ($orderId) {
                $q->where('order_number', 'like', 'SOCO-' . $orderId . '-%')
                  ->orWhere('order_number', 'like', 'SOCO-' . $orderId);
            })
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('frontend.parent.orders')
                ->with('error', 'Order not found.');
        }

        $firstOrder = $orders->first();

        // Order tracking statuses
        $statuses = [
            ['key' => 'pending', 'label' => 'Order Placed', 'icon' => 'shopping-cart'],
            ['key' => 'processing', 'label' => 'Processing', 'icon' => 'tasks'],
            ['key' => 'packed', 'label' => 'Packed', 'icon' => 'box'],
            ['key' => 'shipped', 'label' => 'Shipped', 'icon' => 'truck'],
            ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'check-circle'],
        ];

        // Find current status index
        $currentStatus = strtolower($firstOrder->order_status);
        $currentStatusIndex = 0;
        foreach ($statuses as $index => $status) {
            if ($status['key'] === $currentStatus) {
                $currentStatusIndex = $index;
                break;
            }
        }

        // Format order data
        $order = [
            'id' => $orderId,
            'status' => $firstOrder->order_status,
            'created_at' => $firstOrder->created_at,
            'updated_at' => $firstOrder->updated_at,
            'total' => $orders->sum('total_amount'),
            'tax' => $orders->sum('tax_amount'),
            'subtotal' => $orders->sum('total_amount') - $orders->sum('tax_amount'),
            'customer_name' => $firstOrder->customer_name,
            'customer_address' => $firstOrder->customer_address,
            'customer_phone' => $firstOrder->customer_phone,
            'items' => $orders->map(function ($item) {
                // Get product image
                $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)
                    ->where('school_id', $item->school_id)
                    ->first();

                if (!$product) {
                    $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)->first();
                }

                $productImage = null;
                if ($product) {
                    if ($product->featured_image) {
                        $productImage = $product->featured_image;
                    } elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                        $productImage = $product->media_images[0];
                    } elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                        $productImage = $product->media_gallery[0];
                    }
                }

                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'price' => $item->total_amount,
                    'image' => $productImage ? asset('storage/' . $productImage) : null,
                ];
            })->toArray(),
        ];

        // Fetch the latest return/exchange request for this order grouping
        $itemIds = $orders->pluck('id');
        $returnRequest = \App\Models\Admin\Master\ReturnExchangeRequest::whereIn('order_id', $itemIds)->latest()->first();

        return view('frontend.orders.track', compact('order', 'statuses', 'currentStatusIndex', 'returnRequest'));
    }

    public function returnExchange($orderId, Request $request)
    {
        // Fetch orders from database based on the base order number
        $user = Auth::user();
        $profiles = $user->studentProfiles;
        $studentNames = $profiles->pluck('student_name')->toArray();

        $orders = \App\Models\Admin\Master\Order::whereIn('student_name', $studentNames)
            ->where(function($q) use ($orderId) {
                $q->where('order_number', 'like', 'SOCO-' . $orderId . '-%')
                  ->orWhere('order_number', 'like', $orderId . '%');
            })
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('frontend.parent.orders')
                ->with('error', 'Order not found.');
        }

        // Get the first order for basic info
        $firstOrder = $orders->first();

        // Get pre-selected items from query string
        $selectedItems = $request->query('selected_items', []);

        // Ensure selectedItems is an array (handle single value query param case)
        if (!is_array($selectedItems)) {
            $selectedItems = [$selectedItems];
        }

        // Format order data
        $order = [
            'id' => $orderId,
            'status' => $firstOrder->order_status,
            'created_at' => $firstOrder->created_at,
            'total' => $orders->sum('total_amount'),
            'items' => $orders->map(function ($item) {
                // Try to get product image from ProductMapping
                $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)
                    ->where('school_id', $item->school_id)
                    ->first();

                $image = null;
                if ($product) {
                    if ($product->featured_image) {
                        $image = $product->featured_image;
                    } elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                        $image = $product->media_images[0];
                    } elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                        $image = $product->media_gallery[0];
                    }
                }

                return [
                    'id' => $item->id,
                    'order_number' => $item->order_number,
                    'name' => $item->item_name,
                    'price' => $item->total_amount / $item->quantity,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'image' => $image ? asset('storage/'.$image) : null,
                ];
            })->toArray(),
        ];

        return view('frontend.orders.return-exchange', compact('order', 'selectedItems'));
    }

    public function requestReturnExchange(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'integer|exists:orders,id',
            'reason' => 'required|string',
            'action' => 'required|in:return,exchange',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle photo upload once
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('return-exchange', 'public');
        }

        // Get authenticated student names for security check
        $user = Auth::user();
        $profiles = $user->studentProfiles;
        $studentNames = $profiles->pluck('student_name')->toArray();

        // Fetch selected orders
        $orders = \App\Models\Admin\Master\Order::whereIn('id', $request->selected_items)
            ->whereIn('student_name', $studentNames)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Invalid items selected.');
        }

        // Create return/exchange request for each selected item
        foreach ($orders as $order) {
            // Check if request already exists for this item to prevent duplicates
            $exists = \App\Models\Admin\Master\ReturnExchangeRequest::where('order_id', $order->id)
                ->whereIn('status', ['pending', 'approved', 'received'])
                ->exists();

            if ($exists) {
                continue;
            }

            \App\Models\Admin\Master\ReturnExchangeRequest::create([
                'order_id' => $order->id,
                'type' => $request->action,
                'reason' => $request->reason,
                'photo_path' => $photoPath,
                'status' => 'pending',
                'customer_notes' => $request->reason,
            ]);
        }

        if ($orders->isNotEmpty()) {
             // Create Notification for Admins
            $count = $orders->count();
            $firstOrderNum = $orders->first()->order_number;
            $msg = $count > 1 ? "Request for {$count} items (Order #{$firstOrderNum}...) by {$user->name}" : "Request for Order #{$firstOrderNum} by {$user->name}";

            \App\Models\Notification::create([
                'type' => 'return_request',
                'title' => 'New ' . ucfirst($request->action) . ' Request',
                'message' => $msg,
                'target_role' => null, // Visible to all admins
                'data' => ['order_number' => $firstOrderNum],
            ]);
        }

        return redirect()->route('frontend.parent.orders')
            ->with('success', 'Return/Exchange request submitted successfully for selected items!');
    }

    /**
     * Redirect user based on their role after login
     */
    protected function redirectBasedOnRole(User $user)
    {
        Log::info('Redirecting based on role', [
            'user_id' => $user->id,
            'role' => $user->role,
            'role_name' => $user->getRoleName(),
        ]);

        $route = match($user->role) {
            User::ROLE_PARENT => route('frontend.parent.dashboard'),
            User::ROLE_SCHOOL => route('frontend.school.dashboard'),
            User::ROLE_MASTER_ADMIN => route('master.admin.dashboard'),
            User::ROLE_INVENTORY_ADMIN => route('master.admin.inventory.dashboard'),
            default => route('frontend.parent.dashboard'),
        };

        Log::info('Redirect target', [
            'target' => $route,
        ]);

        return $route;
    }

}
