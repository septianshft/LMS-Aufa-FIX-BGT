<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'trainer.user', 'trainees', 'course_videos'])->orderByDesc('id');

        if ($user->hasRole('trainer')) {
            $query->whereHas('trainer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        $validatedData = array_merge($validated, [
            'slug' => Str::slug($validated['name']),
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


    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
{
    // Optional: add validation/update logic here
    DB::transaction(function () use ($request, $course) {
        $validated = $request->validated();

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $validatedData = array_merge($validated, [
            'slug' => Str::slug($validated['name']),
        ]);

        $course->update($validatedData); // ← tambahkan titik koma di sini

        // Insert course keypoints if available
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();

        try {
            $course->delete();
            DB::commit();

            return redirect()->route('admin.courses.index');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'terjadinya sebuah error');
    }
}
}