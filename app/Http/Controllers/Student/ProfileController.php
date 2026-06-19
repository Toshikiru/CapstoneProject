<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load([
            'studentProfile.bioNotes.counselor',
            'examSessions.exam',
        ]);

        return view('student.profile.index', compact('user'));
    }
}
