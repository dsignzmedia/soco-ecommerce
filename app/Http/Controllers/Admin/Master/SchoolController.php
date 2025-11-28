<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $schools = School::query()
            ->withCount(['grades', 'productMappings'])
            ->when($request->get('status'), fn($q, $status) => $q->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    public function create(): View
    {
        return view('admin.schools.form', [
            'school' => new School(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = Str::slug($data['name']);
        School::create($data);

        return redirect()->route('master.admin.schools.index')
            ->with('status', 'School created.');
    }

    public function edit(School $school): View
    {
        return view('admin.schools.form', [
            'school' => $school,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $this->validateData($request, $school->id);
        $data['slug'] = Str::slug($data['name']);
        $school->update($data);

        return redirect()->route('master.admin.schools.index')
            ->with('status', 'School updated.');
    }

    protected function validateData(Request $request, ?int $schoolId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'board' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,inactive'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

