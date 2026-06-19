<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BioNote;
use App\Models\StudentProfile;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BioNoteController extends Controller
{
    public function store(Request $request, StudentProfile $profile): RedirectResponse
    {
        $request->validate([
            'observation'      => ['required', 'string'],
            'follow_up_actions'=> ['nullable', 'string'],
            'follow_up_date'   => ['nullable', 'date', 'after_or_equal:today'],
            'status'           => ['required', 'in:open,closed,follow_up'],
        ]);

        $note = $profile->bioNotes()->create([
            'counselor_id'     => auth()->id(),
            'observation'      => $request->observation,
            'follow_up_actions'=> $request->follow_up_actions,
            'follow_up_date'   => $request->follow_up_date,
            'status'           => $request->status,
        ]);

        ActivityLogService::log('bionote_created', "Added bio-note for student profile ID: {$profile->id}", BioNote::class, $note->id);

        return back()->with('success', 'Bio-note added successfully.');
    }

    public function update(Request $request, BioNote $bioNote): RedirectResponse
    {
        $request->validate([
            'observation'      => ['required', 'string'],
            'follow_up_actions'=> ['nullable', 'string'],
            'follow_up_date'   => ['nullable', 'date'],
            'status'           => ['required', 'in:open,closed,follow_up'],
        ]);

        $bioNote->update($request->only(['observation', 'follow_up_actions', 'follow_up_date', 'status']));
        ActivityLogService::log('bionote_updated', "Updated bio-note ID: {$bioNote->id}");

        return back()->with('success', 'Bio-note updated successfully.');
    }

    public function destroy(BioNote $bioNote): RedirectResponse
    {
        $profileId = $bioNote->student_profile_id;
        $bioNote->delete();
        ActivityLogService::log('bionote_deleted', "Deleted bio-note ID: {$bioNote->id}");

        return back()->with('success', 'Bio-note deleted.');
    }
}
