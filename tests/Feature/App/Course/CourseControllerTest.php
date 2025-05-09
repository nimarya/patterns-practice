<?php

namespace Tests\Feature\App\Course;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_index_can_be_requested(): void
    {
        $response = $this->get('/courses');

        $response->assertStatus(200);
    }
}
