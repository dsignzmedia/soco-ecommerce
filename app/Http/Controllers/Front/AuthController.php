<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function parentLogin()
    {
        return view('frontend.auth.parent-login');
    }

    public function schoolLogin()
    {
        return view('frontend.auth.school-login');
    }

    public function authenticateSchool(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // TODO: In production, validate credentials against database
        // For now, accept any username/password combination
        // Store school session data
        session(['school_authenticated' => true]);
        session(['school_username' => $request->username]);
        
        // Map username to school name (in production, fetch from database)
        // For now, use a default or map based on username
        $schoolNameMap = [
            'bvb_ajjanur' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur',
            'stanes_icse' => 'Stanes ICSE School',
            'stanes_cbse' => 'Stanes School CBSE',
            'stanes_aihss' => 'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer',
            'bvb_rspuram' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram',
            'snv' => 'Shri Nehru Vidyalaya Matriculation Higher Secondary School (SNV)',
        ];
        
        $schoolName = $schoolNameMap[strtolower($request->username)] ?? 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur';
        session(['school_name' => $schoolName]);

        return redirect()->route('frontend.school.dashboard')
            ->with('success', 'Login successful! Welcome to your dashboard.');
    }

    public function schoolDashboard()
    {
        // Check if school is authenticated
        if (!session('school_authenticated')) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to access the dashboard.');
        }

        // Sample dashboard data (in production, fetch from database)
        $dashboardData = [
            'total_orders' => 1250,
            'total_revenue' => 125000,
            'pending_orders' => 45,
            'completed_orders' => 1205,
        ];

        // School addresses mapping
        $schoolAddresses = [
            'Stanes ICSE School' => 'Peelamedu',
            'Stanes School CBSE' => 'Avinashi Road',
            'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer' => 'Avinashi Road',
            'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram' => 'R S Puram',
            'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur' => 'Ajjanur',
            'Shri Nehru Vidyalaya Matriculation Higher Secondary School (SNV)' => 'R.S. Puram',
        ];

        // Get school name from session or default
        $schoolName = session('school_name', 'School');
        $schoolAddress = $schoolAddresses[$schoolName] ?? '';

        return view('frontend.dashboard.school-dashboard', compact('dashboardData', 'schoolName', 'schoolAddress'));
    }

    public function schoolReports()
    {
        // Check if school is authenticated
        if (!session('school_authenticated')) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to access reports.');
        }

        return view('frontend.dashboard.school-reports');
    }

    public function generateReport(Request $request)
    {
        // Check if school is authenticated
        if (!session('school_authenticated')) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to generate reports.');
        }

        // Validate filters
        $request->validate([
            'date' => 'nullable|date',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2100',
            'grade' => 'nullable|string',
            'product' => 'nullable|string',
            'class' => 'nullable|string',
        ]);

        // Store filter data in session for report generation
        $filters = $request->only(['date', 'month', 'year', 'grade', 'product', 'class']);
        session(['report_filters' => $filters]);

        // Generate sample report data (in production, fetch from database based on filters)
        $reportData = [
            'filters' => $filters,
            'summary' => [
                'total_sales' => 125000,
                'total_orders' => 250,
                'average_order_value' => 500,
                'top_product' => 'School Shirt',
                'top_grade' => 'Grade 5',
            ],
            'chart_data' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'sales' => [15000, 18000, 20000, 22000, 25000, 25000],
            ],
        ];

        session(['report_data' => $reportData]);

        return redirect()->route('frontend.school.reports')
            ->with('report_generated', true)
            ->with('success', 'Report generated successfully!');
    }

    public function downloadReport(Request $request)
    {
        // Check if school is authenticated
        if (!session('school_authenticated')) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to download reports.');
        }

        $format = $request->get('format', 'excel'); // excel or pdf
        $reportData = session('report_data', []);

        // TODO: Generate actual Excel/PDF file
        // For now, return a simple response
        return response()->json([
            'message' => 'Report download functionality will be implemented',
            'format' => $format,
            'data' => $reportData,
        ]);
    }

    public function emailReport(Request $request)
    {
        // Check if school is authenticated
        if (!session('school_authenticated')) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to email reports.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $reportData = session('report_data', []);

        // TODO: Send email with report attachment
        // For now, return success message
        return redirect()->route('frontend.school.reports')
            ->with('success', 'Report will be emailed to ' . $request->email . ' shortly.');
    }

    public function parentDashboard(Request $request)
    {
        // Get profiles from session (in production, this would come from database)
        $profiles = session('student_profiles', []);
        
        // Get selected student ID from query parameter
        $selectedStudentId = $request->get('student_id');
        
        // If no student selected and profiles exist, select the first one
        if (!$selectedStudentId && count($profiles) > 0) {
            $selectedStudentId = $profiles[0]['id'];
        }
        
        // Find selected profile
        $selectedProfile = null;
        if ($selectedStudentId) {
            $selectedProfile = collect($profiles)->firstWhere('id', (int)$selectedStudentId);
        }
        
        // Get purchased products for selected student from actual orders
        $purchasedProducts = [];
        if ($selectedProfile) {
            // Get all orders from session
            $orders = session('orders', []);
            
            // Load all products using the same logic as store page
            $folderProducts = $this->loadProductsFromFolder($selectedProfile['school_name']);
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
                    if (isset($item['profile_id']) && (int)$item['profile_id'] === (int)$selectedProfile['id']) {
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
            $school = \App\Models\Admin\Master\School::where('name', $selectedProfile['school_name'])->first();
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
        $parentPhone = session('parent_phone', '+91 9159413234');

        return view('frontend.dashboard.parent-dashboard', compact('profiles', 'selectedProfile', 'purchasedProducts', 'schoolLogo', 'schoolAddress', 'parentPhone', 'schools'));
    }

    public function accountDetails()
    {
        $parentPhone = session('parent_phone', '+91 9159413234');
        return view('frontend.account.details', compact('parentPhone'));
    }

    public function myAddresses()
    {
        $savedAddresses = session('saved_addresses', []);
        $parentPhone = session('parent_phone', '+91 9159413234');
        return view('frontend.account.addresses', compact('savedAddresses', 'parentPhone'));
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

        $profiles = session('student_profiles', []);

        $profile = [
            'id' => count($profiles) + 1,
            'student_name' => $validated['student_name'],
            'school_name' => $validated['school_name'],
            'grade' => $validated['grade'],
            'section' => $validated['section'],
            'gender' => $validated['gender'],
            'created_at' => now()->toDateTimeString(),
        ];

        $profiles[] = $profile;
        session(['student_profiles' => $profiles]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student profile created successfully!',
                'profile_id' => $profile['id'],
            ]);
        }

        return redirect()->route('frontend.parent.dashboard', ['student_id' => $profile['id']])
            ->with('success', 'Student profile created successfully!');
    }

    public function editProfile($profileId)
    {
        $profiles = session('student_profiles', []);
        $profile = collect($profiles)->firstWhere('id', (int)$profileId);
        
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

        $profiles = session('student_profiles', []);
        $index = null;
        
        foreach ($profiles as $key => $profile) {
            if ((int)$profile['id'] === (int)$profileId) {
                $index = $key;
                break;
            }
        }
        
        if ($index === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found.',
                ], 404);
            }

            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Profile not found.');
        }
        
        $profiles[$index] = [
            'id' => (int)$profileId,
            'student_name' => $validated['student_name'],
            'school_name' => $validated['school_name'],
            'grade' => $validated['grade'],
            'section' => $validated['section'],
            'gender' => $validated['gender'],
            'created_at' => $profiles[$index]['created_at'] ?? now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
        
        session(['student_profiles' => $profiles]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student profile updated successfully!',
                'profile_id' => (int)$profileId,
            ]);
        }

        return redirect()->route('frontend.parent.dashboard', ['student_id' => $profileId])
            ->with('success', 'Student profile updated successfully!');
    }

    public function deleteProfile($profileId)
    {
        $profiles = session('student_profiles', []);
        $filteredProfiles = array_filter($profiles, function($profile) use ($profileId) {
            return (int)$profile['id'] !== (int)$profileId;
        });
        
        // Re-index array
        $filteredProfiles = array_values($filteredProfiles);
        
        session(['student_profiles' => $filteredProfiles]);
        
        // Clear cart if it contains items for this profile
        $cart = session('cart', []);
        $filteredCart = array_filter($cart, function($item) use ($profileId) {
            return isset($item['profile_id']) && (int)$item['profile_id'] !== (int)$profileId;
        });
        session(['cart' => array_values($filteredCart)]);
        
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
        $profileId = $request->get('profile_id');
        $profiles = session('student_profiles', []);
        
        // Find the selected profile
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = collect($profiles)->firstWhere('id', (int)$profileId);
        }

        if (!$selectedProfile) {
            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Please select a student profile first.');
        }

        // Fetch real products from database
        $schoolName = $selectedProfile['school_name'];
        $school = \App\Models\Admin\Master\School::where('name', $schoolName)->first();

        $allProducts = [];

        if ($school) {
            $dbProductsQuery = \App\Models\Admin\Master\ProductMapping::where('school_id', $school->id)
                ->where('status', 'live'); // Assuming 'live' is the active status
            
            // Filter by grade if available in profile
            if (isset($selectedProfile['grade'])) {
                $dbProductsQuery->where(function($q) use ($selectedProfile) {
                    $q->where('grade', $selectedProfile['grade'])
                      ->orWhereNull('grade')
                      ->orWhere('grade', ''); // Handle empty strings if any
                });
            }

            // Filter by gender if available in profile
            if (isset($selectedProfile['gender'])) {
                $gender = strtolower($selectedProfile['gender']);
                $genderMap = [
                    'male' => 'Boys',
                    'female' => 'Girls',
                    'boys' => 'Boys',
                    'girls' => 'Girls'
                ];
                $mappedGender = $genderMap[$gender] ?? $selectedProfile['gender'];

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
                        'boys', 'male' => 'male',
                        'girls', 'female' => 'female',
                        default => 'unisex'
                    },
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'], // Default sizes as DB doesn't have them yet
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
        $profileId = $request->get('profile_id');
        $profiles = session('student_profiles', []);
        
        // Find the selected profile
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = collect($profiles)->firstWhere('id', (int)$profileId);
        }

        if (!$selectedProfile) {
            return redirect()->route('frontend.parent.dashboard')
                ->with('error', 'Please select a student profile first.');
        }

        // Fetch real product from database
        $dbProduct = \App\Models\Admin\Master\ProductMapping::find($productId);

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
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'], // Default sizes
            'tags' => $dbProduct->tag_name ? explode(',', $dbProduct->tag_name) : [],
            'sku' => $dbProduct->id,
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

        // Get cart from session (preserve existing items)
        $cart = session('cart', []);
        
        // Check if the same product with same size and profile already exists in cart
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if (isset($item['product_id']) && $item['product_id'] == $request->product_id &&
                isset($item['size']) && $item['size'] == $request->size &&
                isset($item['profile_id']) && $item['profile_id'] == $request->profile_id) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            // Update quantity of existing item
            $cart[$existingIndex]['quantity'] += $request->quantity;
        } else {
            // Add new item to cart
            $cartItem = [
                'product_id' => $request->product_id,
                'profile_id' => $request->profile_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
                'added_at' => now()->toDateTimeString(),
            ];
            $cart[] = $cartItem;
        }
        
        // Save cart to session
        session(['cart' => $cart]);

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

        // Get cart from session
        $cart = session('cart', []);
        
        // Check if item with same product, profile, and size already exists
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            if (
                (int)$item['product_id'] === (int)$request->product_id &&
                (int)$item['profile_id'] === (int)$request->profile_id &&
                $item['size'] === $request->size
            ) {
                $existingIndex = $index;
                break;
            }
        }
        
        if ($existingIndex !== null) {
            // Increase quantity
            $cart[$existingIndex]['quantity'] += $request->quantity;
            $cart[$existingIndex]['added_at'] = now()->toDateTimeString();
        } else {
            // Add new item
            $cart[] = [
                'product_id' => $request->product_id,
                'profile_id' => $request->profile_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
                'added_at' => now()->toDateTimeString(),
            ];
        }
        
        session(['cart' => $cart]);

        // Redirect to cart page (not checkout) - user can review and then proceed to checkout
        return redirect()->route('frontend.parent.cart')
            ->with('success', 'Product added to cart! Review your order and proceed to checkout.');
    }

    public function cart(Request $request)
    {
        $cart = session('cart', []);
        $profiles = session('student_profiles', []);
        
        // Get all unique product IDs from cart
        $productIds = array_unique(array_column($cart, 'product_id'));
        
        // Fetch products from DB
        $products = \App\Models\Admin\Master\ProductMapping::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $index => $item) {
            if (isset($products[$item['product_id']])) {
                $product = $products[$item['product_id']];
                $itemTotal = $product->price_sale * $item['quantity'];
                $total += $itemTotal;
                
                // Find student name
                $studentName = 'Unknown Student';
                if (isset($item['profile_id'])) {
                    $profile = collect($profiles)->firstWhere('id', (int)$item['profile_id']);
                    if ($profile) {
                        $studentName = $profile['student_name'];
                    }
                }
                
                // Get product image - try multiple fields
            $productImage = null;
            // Try featured_image first
            if ($product->featured_image) {
                $productImage = $product->featured_image;
            }
            // Try media_images array
            elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                $productImage = $product->media_images[0];
            }
            // Try media_gallery array
            elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                $productImage = $product->media_gallery[0];
            }
            
            // Merge item data with product data from DB
            $cartItems[] = array_merge($item, [
                'name' => $product->product_name,
                'price' => $product->price_sale,
                'image' => $productImage ? asset('storage/' . $productImage) : null,
                    'item_total' => $itemTotal,
                    'student_name' => $studentName,
                    'original_index' => $index // Keep track of original index for updates/removes
                ]);
            }
        }

        // For the view's "Buy More" link
        $profileId = $request->get('profile_id', $cart[0]['profile_id'] ?? null);
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = collect($profiles)->firstWhere('id', (int)$profileId);
        }

        return view('frontend.cart.index', compact('cartItems', 'total', 'profiles', 'selectedProfile'));
    }

    public function removeFromCart(Request $request)
    {
        $index = $request->get('index');
        $cart = session('cart', []);
        
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart); // Re-index array
            session(['cart' => $cart]);
        }

        return redirect()->route('frontend.parent.cart')
            ->with('success', 'Item removed from cart.');
    }

    public function checkoutPage(Request $request)
    {
        $cart = session('cart', []);
        $profiles = session('student_profiles', []);
        
        if (empty($cart)) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Your cart is empty.');
        }

        // Get selected items from request (comma-separated indices)
        $selectedItemsStr = $request->get('selected_items', '');
        $selectedIndices = [];
        if (!empty($selectedItemsStr)) {
            $selectedIndices = array_map('intval', explode(',', $selectedItemsStr));
        } else {
            // If no selection, use all items
            $selectedIndices = array_keys($cart);
        }
        
        // Filter cart to only include selected items
        $filteredCart = [];
        foreach ($selectedIndices as $index) {
            if (isset($cart[$index])) {
                $filteredCart[] = $cart[$index];
            }
        }
        
        if (empty($filteredCart)) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Please select at least one item to checkout.');
        }

        // Get profile_id from cart items
        $profileId = null;
        if (!empty($filteredCart)) {
            $profileId = $filteredCart[0]['profile_id'] ?? null;
        }
        
        // Find the selected profile
        $selectedProfile = null;
        if ($profileId) {
            $selectedProfile = collect($profiles)->firstWhere('id', (int)$profileId);
        }
        
        // Get unique product IDs from filtered cart
        $productIds = array_unique(array_column($filteredCart, 'product_id'));
        
        // Fetch products from DB
        $products = \App\Models\Admin\Master\ProductMapping::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        $subtotal = 0;
        $totalTax = 0;
        
        foreach ($filteredCart as $item) {
            if (isset($products[$item['product_id']])) {
                $product = $products[$item['product_id']];
                $itemSubtotal = $product->price_sale * $item['quantity'];
                
                // Calculate tax for this item using price_tax field (which stores the percentage)
                $taxPercentage = $product->price_tax ?? 0;
                $itemTax = ($itemSubtotal * $taxPercentage) / 100;
                $itemTotal = $itemSubtotal + $itemTax;
                
                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
                
                // Get student name from profile
                $studentName = 'Unknown Student';
                if (isset($item['profile_id'])) {
                    $profile = collect($profiles)->firstWhere('id', (int)$item['profile_id']);
                    if ($profile) {
                        $studentName = $profile['student_name'];
                    }
                }
                
                // Get product image - try multiple fields
                $productImage = null;
                // Try featured_image first
                if ($product->featured_image) {
                    $productImage = $product->featured_image;
                }
                // Try media_images array
                elseif ($product->media_images && is_array($product->media_images) && !empty($product->media_images)) {
                    $productImage = $product->media_images[0];
                }
                // Try media_gallery array
                elseif ($product->media_gallery && is_array($product->media_gallery) && !empty($product->media_gallery)) {
                    $productImage = $product->media_gallery[0];
                }
                
                $cartItems[] = array_merge($item, [
                    'name' => $product->product_name,
                    'price' => $product->price_sale,
                    'image' => $productImage ? asset('storage/' . $productImage) : null,
                    'item_total' => $itemTotal,
                    'item_subtotal' => $itemSubtotal,
                    'item_tax' => $itemTax,
                    'tax_percentage' => $taxPercentage,
                    'student_name' => $studentName,
                ]);
            }
        }
        
        $total = $subtotal + $totalTax;

        // Get saved addresses from session
        $savedAddresses = session('saved_addresses', []);
        
        // Store selected indices in session for processCheckout
        session(['checkout_selected_indices' => $selectedIndices]);
        
        return view('frontend.checkout.index', compact('cartItems', 'total', 'subtotal', 'totalTax', 'profiles', 'selectedProfile', 'savedAddresses'));
    }

    public function processCheckout(Request $request)
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Your cart is empty.');
        }
        
        // Get selected items from session (set during checkoutPage)
        $selectedIndices = array_map('intval', session('checkout_selected_indices', []));
        
        // Filter cart to only include selected items
        $filteredCart = [];
        if (!empty($selectedIndices)) {
            foreach ($selectedIndices as $index) {
                if (isset($cart[$index])) {
                    $filteredCart[] = $cart[$index];
                }
            }
        } else {
            // If no selection was made, use all items
            $filteredCart = $cart;
        }
        
        if (empty($filteredCart)) {
            return redirect()->route('frontend.parent.cart')
                ->with('error', 'Please select at least one item to checkout.');
        }
        
        $savedAddresses = session('saved_addresses', []);

        if (empty($savedAddresses) && !$request->has('name')) {
            return redirect()->route('frontend.parent.checkout')
                ->with('error', 'Please add a shipping address before placing your order.');
        }
        
        try {
            // Save address if it's a new one
            $selectedAddressIndex = $request->get('selected_address');
            
            $shippingAddress = [];
            if ($selectedAddressIndex !== null && isset($savedAddresses[$selectedAddressIndex])) {
                // Use saved address
                $shippingAddress = $savedAddresses[$selectedAddressIndex];
            } else {
                // Validate new address
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
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
                    'email' => $request->email,
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
                
                // Save to session
                $savedAddresses[] = $shippingAddress;
                session(['saved_addresses' => $savedAddresses]);
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
            
            // Get profile_id from cart (all items should have same profile_id)
            $profileId = !empty($cart) ? ($cart[0]['profile_id'] ?? null) : null;
            
            // Get student profile information
            $profiles = session('student_profiles', []);
            $studentProfile = null;
            if ($profileId) {
                $studentProfile = collect($profiles)->firstWhere('id', (int)$profileId);
            }

            // DB Transaction to ensure data consistency
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            foreach ($filteredCart as $index => $item) {
                // Fetch product details from DB
                $product = \App\Models\Admin\Master\ProductMapping::find($item['product_id']);
                
                if (!$product) {
                    continue; // Skip if product not found
                }

                // Create Order Record with unique suffix for each item
                // Format: SOCO-[ID]-[Index]
                $uniqueOrderNumber = $orderNumber . '-' . ($index + 1);

                \App\Models\Admin\Master\Order::create([
                    'order_number' => $uniqueOrderNumber,
                    'school_id' => $studentProfile['school_id'] ?? $product->school_id,
                    'order_date' => now(),
                    'student_name' => $studentProfile['student_name'] ?? 'Unknown',
                    'grade' => $studentProfile['grade'] ?? $product->grade,
                    'category' => $product->category ?? 'General',
                    'item_name' => $product->product_name,
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'customer_name' => $shippingAddress['name'],
                    'customer_address' => $shippingAddress['address'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['state'] . ' - ' . $shippingAddress['pincode'],
                    'customer_phone' => $shippingAddress['phone'],
                    'customer_email' => $shippingAddress['email'],
                    'total_amount' => $product->price_sale * $item['quantity'], // Use current price from DB
                    'tax_amount' => 0,
                    'shipping_cost' => 0,
                    'payment_status' => 'pending',
                    'order_status' => 'processing',
                ]);

                // Decrement Inventory
                $product->decrement('inventory_stock', $item['quantity']);
            }
            
            \Illuminate\Support\Facades\DB::commit();
            
            // Clear cart (or selected items)
            if (empty($selectedIndices)) {
                session()->forget('cart');
            } else {
                $remainingCart = [];
                foreach ($cart as $index => $item) {
                    if (!in_array($index, $selectedIndices)) {
                        $remainingCart[] = $item;
                    }
                }
                session(['cart' => $remainingCart]); // Reindex array
            }
            
            // Clear checkout session data
            session()->forget(['checkout_selected_indices', 'orders']);

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

    public function saveAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
        ]);

        $savedAddresses = session('saved_addresses', []);
        
        // Determine the display name for address type
        $addressTypeDisplay = $request->address_type ?? 'home';
        if ($request->address_type === 'others' && $request->custom_address_type) {
            $addressTypeDisplay = $request->custom_address_type;
        }
        
        $newAddress = [
            'name' => $request->name,
            'email' => $request->email,
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
        
        $savedAddresses[] = $newAddress;
        session(['saved_addresses' => $savedAddresses]);

        return response()->json(['success' => true, 'message' => 'Address saved successfully']);
    }

    public function updateAddress(Request $request, $addressId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
        ]);

        $savedAddresses = session('saved_addresses', []);
        
        if (!isset($savedAddresses[$addressId])) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }
        
        // Determine the display name for address type
        $addressTypeDisplay = $request->address_type ?? 'home';
        if ($request->address_type === 'others' && $request->custom_address_type) {
            $addressTypeDisplay = $request->custom_address_type;
        }
        
        // Update the address
        $savedAddresses[$addressId] = [
            'name' => $request->name,
            'email' => $request->email,
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
        
        session(['saved_addresses' => $savedAddresses]);

        return response()->json(['success' => true, 'message' => 'Address updated successfully']);
    }

    public function deleteAddress($addressId)
    {
        $savedAddresses = session('saved_addresses', []);
        
        if (isset($savedAddresses[$addressId])) {
            unset($savedAddresses[$addressId]);
            $savedAddresses = array_values($savedAddresses); // Re-index array
            session(['saved_addresses' => $savedAddresses]);
            
            return redirect()->route('frontend.parent.addresses')
                ->with('success', 'Address deleted successfully!');
        }
        
        return redirect()->route('frontend.parent.addresses')
            ->with('error', 'Address not found.');
    }

    public function orders()
    {
        // Get logged in parent's phone number (assuming auth logic uses phone)
        // Since we don't have a strict parent auth system yet, we'll use the session 'student_profiles' 
        // to find orders related to the students.
        // Ideally, we should have a parent_id or user_id.
        
        $profiles = session('student_profiles', []);
        $studentNames = collect($profiles)->pluck('student_name')->toArray();
        
        // Fetch orders from DB where student_name matches any of the profiles
        // This is a temporary linkage based on student name. 
        // A better approach would be linking by parent phone/email if available in session.
        
        $orders = \App\Models\Admin\Master\Order::whereIn('student_name', $studentNames)
            ->orderByDesc('created_at')
            ->get();

        // Group orders by the base order number (before the hyphen)
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
                // No longer attach a single request at order level; per-item will be added below
                'items' => $items->map(function ($item) use ($returnRequests) {
                    // Get product image
                    $product = \App\Models\Admin\Master\ProductMapping::where('product_name', $item->item_name)
                        ->where('school_id', $item->school_id)
                        ->first();
                    
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
                        'price' => $item->total_amount / $item->quantity, // Unit price
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
        $profiles = session('student_profiles', []);
        $studentNames = collect($profiles)->pluck('student_name')->toArray();
        
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
            'items' => $orders->map(function ($item) {
                return [
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'price' => $item->total_amount,
                ];
            })->toArray(),
        ];

        return view('frontend.orders.track', compact('order', 'statuses', 'currentStatusIndex'));
    }

    public function returnExchange($orderId, Request $request)
    {
        // Fetch orders from database based on the base order number
        $profiles = session('student_profiles', []);
        $studentNames = collect($profiles)->pluck('student_name')->toArray();
        
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
                if ($product && $product->product_images) {
                    $images = is_string($product->product_images) 
                        ? json_decode($product->product_images, true) 
                        : $product->product_images;
                    $image = is_array($images) && !empty($images) ? $images[0] : null;
                }
                
                return [
                    'id' => $item->id,
                    'order_number' => $item->order_number,
                    'name' => $item->item_name,
                    'price' => $item->total_amount / $item->quantity,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'image' => $image,
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
        $profiles = session('student_profiles', []);
        $studentNames = collect($profiles)->pluck('student_name')->toArray();

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

        return redirect()->route('frontend.parent.orders')
            ->with('success', 'Return/Exchange request submitted successfully for selected items!');
    }

}
