<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTrainee;
use App\Models\SubscribeTransaction;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index(){
        $user = Auth::user();
        $coursesQuery = Course::query();

        if($user->hasRole('trainer')) {
            $coursesQuery->whereHas('trainer', function ($query) use ($user) {
                $query->where('user_id', $user->id);

            });
            $trainees = CourseTrainee::whereIn('course_id', $coursesQuery->select('id'))
            ->distinct('user_id')
            ->count('user_id');

        } else {
            $trainees = CourseTrainee::distinct('user_id')
            ->count('user_id');
        }

        $courses = $coursesQuery->count();

        $categories = Category::count();
        $transactions = SubscribeTransaction::count();
        $trainers = Trainer::count();

        return view('dashboard', compact('categories', 'courses', 'transactions', 'trainees', 'trainers'));

    }
}
