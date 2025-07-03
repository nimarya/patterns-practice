<?php

namespace App\Services\Course\Builders;

use App\Models\Course;
use App\Models\Module;

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

    public function addModules(int $modulesNumber): void
    {
        for ($i = 1; $i <= $modulesNumber; $i++) {
            $module = new Module([
                'name'        => 'module '. $i,
                'description' => 'module description '. $i,
                'course_id'   => $this->videoCourse->id,
            ]);
            $module->save();
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