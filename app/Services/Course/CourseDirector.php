<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Services\Course\Builders\CourseBuilderInterface;

class CourseDirector
{
    public const MODULES_MAJOR_SEMESTER = 4;
    public const MODULES_MAJOR_YEAR     = 8;
    public const MODULES_MINOR_SEMESTER = 2;
    public const MODULES_MINOR_YEAR     = 4;

    public function buildCourse(CourseBuilderInterface $builder, array $attributes, int $modulesNumber): Course
    {
        $builder->reset();
        $builder->setName($attributes['name']);
        $builder->setDescription($attributes['description']);
        $builder->setAuthor($attributes['author_id']);
        $builder->saveCourse();
        $builder->addLessons($modulesNumber);
        return $builder->getCourse();
    }
}