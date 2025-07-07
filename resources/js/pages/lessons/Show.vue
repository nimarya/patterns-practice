<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
    lesson: {
        id: number;
        name: string;
        description: string;
        course: {
            id: number;
            name: string;
        };
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: '/courses' },
    { title: props.lesson.course.name, href: `/courses/${props.lesson.course.id}` },
    { title: props.lesson.name, href: '' },
];
</script>

<template>
    <Head :title="props.lesson.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-2">{{ props.lesson.name }}</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-4">{{ props.lesson.description }}</p>

            <Link
                :href="`/courses/${props.lesson.course.id}`"
                class="text-rose-800 hover:text-rose-600"
            >
                Back to Course
            </Link>
        </div>
    </AppLayout>
</template>
