<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeTransaction;

class FrontController extends Controller
{
    /**
     * Halaman depan menampilkan semua course
     */
    public function index()
    {
        $courses = Course::with(['category', 'trainer', 'trainees'])
                         ->orderByDesc('id')
                         ->get();

        return view('front.index', compact('courses'));
    }

    /**
     * Detail course
     */
    public function details(Course $course)
    {
        return view('front.details', compact('course'));
    }

    /**
     * Halaman pembelajaran untuk course tertentu
     */
    public function learning(Course $course, $courseVideoId)
{
    $user = Auth::user();

    // Cek apakah user punya akses ke course ini
    $hasAccess = SubscribeTransaction::where('user_id', $user->id)
        ->where('is_paid', true)
        ->where(function ($query) use ($course) {
            $query->whereNull('course_id') // akses ke semua kelas
                  ->orWhere('course_id', $course->id); // atau hanya kelas ini
        })
        ->exists();

    // Jika tidak punya akses dan course berbayar, redirect
    if (!$hasAccess && $course->price > 0) {
        return redirect()->route('front.pricing', compact('course'))
                         ->with('error', 'Kamu belum membeli akses ke kelas ini.');
    }

    // Ambil video dari relasi
    $video = $course->course_videos->firstWhere('id', $courseVideoId);

    // Jika video tidak ditemukan, lempar 404
    if (!$video) {
        abort(404, 'Video tidak ditemukan.');
    }

    // Tambahkan course ke relasi user jika belum ada (untuk pelacakan/history)
    $user->courses()->syncWithoutDetaching($course->id);

    return view('front.learning', compact('course', 'video'));
}

public function pricing(Course $course)
{
    return view('front.pricing', compact('course'));
}


}
