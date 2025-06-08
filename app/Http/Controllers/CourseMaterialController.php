<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseMaterialRequest;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\DB;

class CourseMaterialController extends Controller
{
    /**
     * Store a newly uploaded course material.
     */
    public function store(StoreCourseMaterialRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $path = $request->file('file')->store('materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();

            CourseMaterial::create($validated);
        });

        return back()->with('success', 'Material uploaded successfully.');
    }
}
