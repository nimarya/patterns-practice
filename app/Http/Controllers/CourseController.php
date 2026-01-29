<?php

namespace App\Http\Controllers;

use App\Http\Requests\Courses\StoreCourseRequest;
use App\Models\Course;
use App\Services\Course\Builders\VideoCourseBuilder;
use App\Services\Course\CourseDirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $ownedCourseIds = Auth::user()
            ? Auth::user()->courses()->pluck('courses.id')->all()
            : [];
        $authoredCourseIds = Auth::user()
            ? Course::where('user_id', Auth::id())->pluck('id')->all()
            : [];

        return Inertia::render('courses/Index', [
            'courses' => Course::select('id', 'name', 'description', 'photo', 'price_cents', 'currency')->get(),
            'ownedCourseIds' => $ownedCourseIds,
            'authoredCourseIds' => $authoredCourseIds,
            'can' => [
                'createCourse' => fn () => Auth::user()?->can('create course') ?? false,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('courses/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $priceCents = (int) round(((float) $validated['price']) * 100);
        $currency = strtolower($validated['currency'] ?? 'usd');

        $director = new CourseDirector;
        $builder = new VideoCourseBuilder;

        $attributes = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'author_id' => $validated['user_id'],
            'price_cents' => $priceCents,
            'currency' => $currency,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('courses', 'public');
            $attributes['photo'] = $path;
        }

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
    public function show(Course $course): Response
    {
        $this->authorize('view', $course);

        return Inertia::render('courses/Show', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'lessons' => $course->lessons->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'description' => $m->description,
                ]),
            ],
        ]);
    }

    public function myCourses(Request $request): Response
    {
        $user = Auth::user();
        $notice = $request->query('notice');

        return Inertia::render('courses/MyCourses', [
            'courses' => Course::query()
                ->where('user_id', $user->id)
                ->orWhereHas('users', fn ($query) => $query->whereKey($user->id))
                ->select('courses.id', 'courses.name', 'courses.description', 'courses.photo', 'courses.price_cents', 'courses.currency')
                ->distinct('courses.id')
                ->get(),
            'notice' => $notice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }
}
