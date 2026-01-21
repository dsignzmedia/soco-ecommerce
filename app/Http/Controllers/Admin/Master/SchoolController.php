<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\School;
use App\Models\Admin\Master\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $schools = School::query()
            // Global scope automatically filters has_deleted = 0
            ->withCount(['grades', 'productMappings'])
            ->when($request->get('status'), fn($q, $status) => $q->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    public function create(): View
    {
        $zones = ShippingZone::orderBy('name')->get();
        
        return view('admin.schools.form', [
            'school' => new School(),
            'mode' => 'create',
            'zones' => $zones,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = Str::slug($data['name']);
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools', 'public');
        }

        $school = School::create($data);

        // Create user account for the school with role 1 (OTP-based login)
        if (!empty($data['contact_email'])) {
            try {
                $user = \App\Models\User::create([
                    'name' => $data['name'],
                    'email' => $data['contact_email'],
                    'phone' => $data['contact_phone'] ?? null,
                    'password' => bcrypt(Str::random(32)), // Random password (won't be used - OTP login)
                    'role' => \App\Models\User::ROLE_SCHOOL,
                    'school_id' => $school->id,
                    'email_verified_at' => now(),
                ]);

                return redirect()->route('master.admin.schools.index')
                    ->with('status', 'School created successfully. School can login using OTP sent to their email.');
            } catch (\Exception $e) {
                \Log::error('Failed to create user account for school: ' . $e->getMessage());
                
                return redirect()->route('master.admin.schools.index')
                    ->with('status', 'School created but user account creation failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('master.admin.schools.index')
            ->with('status', 'School created successfully.');
    }

    public function edit(School $school): View
    {
        $zones = ShippingZone::orderBy('name')->get();
        
        return view('admin.schools.form', [
            'school' => $school,
            'mode' => 'edit',
            'zones' => $zones,
        ]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $this->validateData($request, $school->id);
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools', 'public');
        }

        $school->update($data);

        // Sync changes to the associated User account (School Admin)
        if (!empty($data['contact_email'])) {
             $user = \App\Models\User::where('school_id', $school->id)
                ->where('role', \App\Models\User::ROLE_SCHOOL)
                ->first();
             
             if ($user) {
                 // Update user details to match school details
                 $user->update([
                     'name' => $data['name'],
                     'email' => $data['contact_email'],
                     'phone' => $data['contact_phone'] ?? $user->phone,
                 ]);
             }
        }

        return redirect()->route('master.admin.schools.index')
            ->with('status', 'School updated successfully.');
    }

    protected function validateData(Request $request, ?int $schoolId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'board' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,inactive'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'shipping_zone_id' => ['nullable', 'exists:shipping_zones,id'],
        ]);
    }

    public function destroy(School $school): RedirectResponse
    {
        // 1. Soft Delete: Mark School as Deleted using has_deleted column (1 = deleted)
        $school->update(['has_deleted' => 1]);

        // 2. Archive all associated Products
        $school->productMappings()->update(['status' => 'archived']);

        // 3. Soft delete school users (set has_deleted = 1 for users where school_id matches)
        \App\Models\User::where('school_id', $school->id)
            ->where('role', \App\Models\User::ROLE_SCHOOL)
            ->update(['has_deleted' => 1]);

        return redirect()->route('master.admin.schools.index')
            ->with('status', 'School deleted successfully. All associated products have been archived and school users have been deactivated.');
    }

    /**
     * Get deletion statistics for a school (for modal display)
     */
    public function getDeletionStats(School $school): \Illuminate\Http\JsonResponse
    {
        // Count products created for this school
        $productsCount = $school->productMappings()->count();

        // Count orders related to this school
        $ordersCount = \App\Models\Admin\Master\Order::where('school_id', $school->id)->count();

        // Count students enrolled - check StudentProfile with matching school_name
        // Note: StudentProfile uses 'school_name' (string), not school_id, so this is fragile
        $studentsCount = \App\Models\StudentProfile::where('school_name', $school->name)->count();

        // Also count from orders (unique student names for this school)
        $studentNamesFromOrders = \App\Models\Admin\Master\Order::where('school_id', $school->id)
            ->distinct()
            ->count('student_name');

        // Use the higher count as the estimate
        $studentsCount = max($studentsCount, $studentNamesFromOrders);

        return response()->json([
            'products' => $productsCount,
            'orders' => $ordersCount,
            'students' => $studentsCount,
        ]);
    }
}

