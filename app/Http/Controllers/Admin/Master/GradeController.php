<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Grade;
use App\Models\Admin\Master\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(School $school): View
    {
        $grades = $school->grades()->get();

        return view('admin.schools.grades', compact('school', 'grades'));
    }

    public function store(Request $request, School $school): RedirectResponse
    {
        $data = $this->validateGrade($request);
        $school->grades()->create($data);

        return back()->with('status', 'Grade added.');
    }

    public function update(Request $request, School $school, Grade $grade): RedirectResponse
    {
        abort_unless($grade->school_id === $school->id, 404);
        $grade->update($this->validateGrade($request));

        return back()->with('status', 'Grade updated.');
    }

    public function destroy(School $school, Grade $grade): RedirectResponse
    {
        abort_unless($grade->school_id === $school->id, 404);
        $grade->delete();

        return back()->with('status', 'Grade deleted.');
    }

    protected function validateGrade(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:1'],
            'gender_rule' => ['required', 'in:boys,girls,unisex'],
        ]);
    }
}

