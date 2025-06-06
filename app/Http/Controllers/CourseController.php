<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\CourseMode;
use App\Models\CourseLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'trainer.user', 'trainees', 'course_videos', 'mode', 'level'])->orderByDesc('id');

        if ($user->hasRole('trainer')) {
            $query->whereHas('trainer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();
        return view('admin.courses.create', compact('categories', 'modes', 'levels'));
    }

    public function store(StoreCourseRequest $request)
{
    $trainer = Trainer::where('user_id', Auth::id())->first();

    if (!$trainer) {
        return redirect()->route('admin.courses.index')
            ->withErrors(['trainer' => 'Unauthorized or invalid trainer']);
    }

    DB::transaction(function () use ($request, $trainer) {
        $validated = $request->validated();

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $count = Course::where('slug', 'like', "{$slug}%")->count();
        $slug = $count ? "{$slug}-{$count}" : $slug;

        $validatedData = array_merge($validated, [
            'slug' => $slug,
            'trainer_id' => $trainer->id,
            'price' => $validated['price'] ?? 0,
        ]);

        // Create course
        $course = Course::create($validatedData);

        // Insert course keypoints if available
        if (!empty($validated['course_keypoints'])) {
            foreach ($validated['course_keypoints'] as $keypointText) {
                if (!empty($keypointText)) {
                    $course->course_keypoints()->create([
                        'name' => $keypointText
                    ]);
                }
            }
        }
    });

    return redirect()->route('admin.courses.index')->with('success', 'Course successfully created.');
}


    public function show(Course $course)
    {
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();
        return view('admin.courses.edit', compact('course', 'categories', 'modes', 'levels'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
{
    DB::transaction(function () use ($request, $course) {
        $validated = $request->validated();

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Generate unique slug, excluding current course ID
        $slug = Str::slug($validated['name']);
        $count = Course::where('slug', 'like', "{$slug}%")->where('id', '!=', $course->id)->count();
        $slug = $count ? "{$slug}-{$count}" : $slug;

        $validatedData = array_merge($validated, [
            'slug' => $slug,
        ]);

        $course->update($validatedData);

        // Update course keypoints
        if (!empty($validated['course_keypoints'])) {
            $course->course_keypoints()->delete();
            foreach ($validated['course_keypoints'] as $keypointText) {
                if (!empty($keypointText)) {
                    $course->course_keypoints()->create([
                        'name' => $keypointText
                    ]);
                }
            }
        }
    });

    return redirect()->route('admin.courses.show', $course);
}


    public function destroy(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $course->delete();
            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'An error occurred while deleting the course.');
        }
    }
}
