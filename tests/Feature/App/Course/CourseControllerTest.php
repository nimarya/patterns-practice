<?php

namespace Tests\Feature\App\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_can_be_requested(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('courses.index'));

        $response->assertOk();
    }

    public function test_show_can_be_requested(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $user->courses()->attach($course->id);

        $response = $this->actingAs($user)
            ->get(route('courses.show', $course));

        $response->assertOk();
    }

    public function test_course_can_be_stored_in_database(): void
    {
        $user = User::factory()->create();

        $user->assignRole($this->getTeacherRole());

        $data = [
            'name' => 'Test course',
            'description' => 'Test course description',
            'type' => 'major_semester',
            'user_id' => $user->id,
            'price' => 99.99,
            'currency' => 'usd',
        ];

        $response = $this->actingAs($user)
            ->post(route('courses.store'), $data);

        $response->assertRedirectToRoute('courses.index');

        $this->assertDatabaseHas('courses', [
            'name' => 'Test course',
            'description' => 'Test course description',
            'user_id' => $user->id,
            'price_cents' => 9999,
            'currency' => 'usd',
        ]);

        $course = Course::where('name', 'Test course')->first();
        $this->assertNotNull($course);

        $this->assertDatabaseCount('lessons', 16);
        $this->assertTrue($course->lessons()->count() === 16);
    }

    public function test_my_courses_only_shows_owned_courses(): void
    {
        $user = User::factory()->create();
        $ownedCourse = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $authoredCourse = Course::factory()->create(['user_id' => $user->id]);

        $user->courses()->attach($ownedCourse->id);

        $response = $this->actingAs($user)->get(route('courses.mine'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('courses/MyCourses')
            ->has('courses', 2)
            ->where('courses', function ($courses) use ($ownedCourse, $authoredCourse): bool {
                $ids = collect($courses)->pluck('id')->sort()->values()->all();
                $expected = collect([$ownedCourse->id, $authoredCourse->id])->sort()->values()->all();

                return $ids === $expected;
            })
        );
    }

    public function test_show_is_forbidden_for_users_without_access(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('courses.show', $course));

        $response->assertForbidden();
    }

    private function getTeacherRole(): Role
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::create(['name' => 'create course']);
        $role = Role::create(['name' => 'teacher']);

        return $role->givePermissionTo($permission);
    }
}
