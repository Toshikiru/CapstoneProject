<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('studentProfile')
            ->where('role', 'student')
            ->whereNull('deleted_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($course = $request->get('course')) {
            $query->whereHas('studentProfile', fn($q) => $q->where('course', $course));
        }

        if ($year = $request->get('year_level')) {
            $query->whereHas('studentProfile', fn($q) => $q->where('year_level', $year));
        }

        if ($status = $request->get('admission_status')) {
            $query->whereHas('studentProfile', fn($q) => $q->where('admission_status', $status));
        }

        $students = $query->latest()->paginate(20)->withQueryString();

        $courses   = StudentProfile::distinct()->pluck('course');
        $yearLevels = StudentProfile::distinct()->pluck('year_level');

        return view('admin.students.index', compact('students', 'courses', 'yearLevels'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'student_id' => $request->student_id,
                'name'       => $request->name,
                'password'   => $request->password,
                'role'       => 'student',
                'is_active'  => true,
            ]);

            StudentProfile::create([
                'user_id'                 => $user->id,
                'student_id_number'       => $request->student_id,
                'first_name'              => $request->first_name,
                'middle_name'             => $request->middle_name,
                'last_name'               => $request->last_name,
                'suffix'                  => $request->suffix,
                'sex'                     => $request->sex,
                'date_of_birth'           => $request->date_of_birth,
                'address'                 => $request->address,
                'contact_number'          => $request->contact_number,
                'guardian_name'           => $request->guardian_name,
                'guardian_contact_number' => $request->guardian_contact_number,
                'course'                  => $request->course,
                'year_level'              => $request->year_level,
            ]);

            ActivityLogService::log('student_created', "Created student: {$user->name}", User::class, $user->id);
        });

        return redirect()->route('admin.students.index')->with('success', 'Student account created successfully.');
    }

    public function show(User $student): View
    {
        $student->load(['studentProfile.bioNotes.counselor', 'examSessions.exam']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(User $student): View
    {
        $student->load('studentProfile');
        return view('admin.students.edit', compact('student'));
    }

    public function update(StoreStudentRequest $request, User $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student) {
            $student->update([
                'student_id' => $request->student_id,
                'name'       => $request->name,
            ]);

            if ($request->filled('password')) {
                $student->update(['password' => $request->password]);
            }

            $student->studentProfile->update([
                'student_id_number'       => $request->student_id,
                'first_name'              => $request->first_name,
                'middle_name'             => $request->middle_name,
                'last_name'               => $request->last_name,
                'suffix'                  => $request->suffix,
                'sex'                     => $request->sex,
                'date_of_birth'           => $request->date_of_birth,
                'address'                 => $request->address,
                'contact_number'          => $request->contact_number,
                'guardian_name'           => $request->guardian_name,
                'guardian_contact_number' => $request->guardian_contact_number,
                'course'                  => $request->course,
                'year_level'              => $request->year_level,
            ]);

            ActivityLogService::log('student_updated', "Updated student: {$student->name}", User::class, $student->id);
        });

        return redirect()->route('admin.students.show', $student)->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student): RedirectResponse
    {
        $name = $student->name;
        $student->delete();
        ActivityLogService::log('student_deleted', "Deleted student: {$name}", User::class, $student->id);

        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    public function toggleActive(User $student): RedirectResponse
    {
        $student->update(['is_active' => ! $student->is_active]);
        $status = $student->is_active ? 'activated' : 'deactivated';
        ActivityLogService::log("student_{$status}", "Student {$status}: {$student->name}", User::class, $student->id);

        return back()->with('success', "Student account {$status} successfully.");
    }

    public function resetPassword(Request $request, User $student): RedirectResponse
    {
        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);

        $student->update(['password' => $request->password]);
        ActivityLogService::log('password_reset', "Password reset for: {$student->name}", User::class, $student->id);

        return back()->with('success', 'Password reset successfully.');
    }
}
