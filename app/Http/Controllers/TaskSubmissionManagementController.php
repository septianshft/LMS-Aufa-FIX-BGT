<?php

namespace App\Http\Controllers;

use App\Models\{Course, TaskSubmission};
use Illuminate\Http\Request;

class TaskSubmissionManagementController extends Controller
{
    public function index(Course $course)
    {
        $submissions = TaskSubmission::with(['task.module.course', 'user'])
            ->whereHas('task.module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->get();

        return view('admin.task_submissions.index', compact('course', 'submissions'));
    }

    public function update(Request $request, TaskSubmission $submission)
    {
        $data = $request->validate(['grade' => 'nullable|integer']);
        $submission->update($data);
        return back();
    }
}
