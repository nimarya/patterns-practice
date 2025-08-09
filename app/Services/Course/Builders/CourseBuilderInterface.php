<?php

namespace App\Services\Course\Builders;

use App\Models\Course;

interface CourseBuilderInterface
{
    public function reset(): void;

    public function setName(string $name): void;

    public function setDescription(string $description): void;

    public function setAuthor(int $userId): void;

    public function setPhoto(?string $photo = null): void;

    public function addLessons(int $modulesNumber): void;

    public function saveCourse(): void;

    public function getCourse(): Course;
}