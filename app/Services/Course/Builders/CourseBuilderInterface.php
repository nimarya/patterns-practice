<?php

namespace App\Services\Course\Builders;

interface CourseBuilderInterface
{
    public function createCourse();
    public function addModule();
    public function addLesson();
}