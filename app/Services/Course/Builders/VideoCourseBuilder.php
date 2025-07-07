<?php

namespace App\Services\Course\Builders;

use App\Models\Course;
use App\Models\Lesson;

class VideoCourseBuilder implements CourseBuilderInterface
{
    private Course $videoCourse;

    public function reset(): void
    {
        $this->videoCourse = new Course();
    }

    public function setName(string $name): void
    {
        $this->videoCourse->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->videoCourse->description = $description;
    }

    public function setAuthor(int $userId): void
    {
        $this->videoCourse->user_id = $userId;
    }

    public function addLessons(int $modulesNumber): void
    {
        for ($i = 1; $i <= $modulesNumber * 4; $i++) {
            $lesson = new Lesson([
                'name'        => 'lesson '. $i,
                'description' => 'lesson description '. $i,
                'course_id'   => $this->videoCourse->id,
            ]);
            $lesson->save();
        }
    }

    public function saveCourse(): void
    {
        $this->videoCourse->save();
    }

    public function getCourse(): Course
    {
        return $this->videoCourse;
    }
}