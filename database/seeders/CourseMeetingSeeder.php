<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseMeeting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseMeetingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Course::take(2)->get() as $course) {
            CourseMeeting::firstOrCreate([
                'course_id' => $course->id,
                'title' => 'Kickoff Meeting',
            ], [
                'start_datetime' => Carbon::now()->addDays(1),
                'end_datetime' => Carbon::now()->addDays(1)->addHours(2),
                'location' => 'Room 101',
            ]);

            CourseMeeting::firstOrCreate([
                'course_id' => $course->id,
                'title' => 'Second Session',
            ], [
                'start_datetime' => Carbon::now()->addDays(3),
                'end_datetime' => Carbon::now()->addDays(3)->addHours(2),
                'location' => 'Room 102',
            ]);
        }
    }
}
