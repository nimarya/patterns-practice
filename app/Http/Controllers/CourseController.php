<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\Course\Builders\VideoCourseBuilder;
use App\Services\Course\CourseDirector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('courses/Index', [
            'courses' => Course::select('id', 'name', 'description')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('courses/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'user_id' => 'required|exists:App\Models\User,id',
            'type' => 'required|in:major_semester,major_year,minor_semester,minor_year',
        ]);
    
        $director = new CourseDirector();
        $builder = new VideoCourseBuilder();
    
        $attributes = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'author_id' => $validated['user_id'],
        ];
    
        // Выбор типа курса
        switch ($validated['type']) {
            case 'major_semester':
                $course = $director->buildCourse($builder, $attributes, CourseDirector::MODULES_MAJOR_SEMESTER);
                break;
            case 'major_year':
                $course = $director->buildCourse($builder, $attributes, CourseDirector::MODULES_MAJOR_YEAR);
                break;
            case 'minor_semester':
                $course = $director->buildCourse($builder, $attributes, CourseDirector::MODULES_MINOR_SEMESTER);
                break;
            case 'minor_year':
                $course = $director->buildCourse($builder, $attributes, CourseDirector::MODULES_MINOR_YEAR);
                break;
            default:
                abort(400, 'Invalid course type');
        }
    
        $course->save();
    
        return redirect(route('courses.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return Inertia::render('courses/Show', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'modules' => $course->modules->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                ]),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
