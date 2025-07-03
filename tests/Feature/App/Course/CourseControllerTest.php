<?php

namespace Tests\Feature\App\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response = $this->actingAs($user)
            ->get(route('courses.show', $course));

        $response->assertOk();
    }

    public function test_course_can_be_stored_in_database(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Test course',
            'description' => 'Test course description',
            'type' => 'major_semester',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)
            ->post(route('courses.store'), $data);

        $response->assertRedirectToRoute('courses.index');

        $this->assertDatabaseHas('courses', [
            'name' => 'Test course',
            'description' => 'Test course description',
            'user_id' => $user->id,
        ]);

        $course = Course::where('name', 'Test course')->first();
        $this->assertNotNull($course);

        $this->assertDatabaseCount('modules', 4);

        $this->assertTrue($course->modules()->count() === 4);
    }
}
