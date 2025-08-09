<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Courses', href: '/courses' },
];

defineProps<{
    courses: {
        id: number;
        name: string;
        description: string;
        photo: string;
    }[]
}>();
</script>

<template>
    <Head title="Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-between items-center p-4">
            <h1 class="text-2xl font-bold">Courses</h1>
            <Link
                href="/courses/create"
                class="bg-rose-800 text-white px-4 py-2 rounded-xl hover:bg-rose-600"
            >
                Create Course
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="course in courses"
                :key="course.id"
                :href="`/courses/${course.id}`"
                class="rounded-xl border border-gray-200 dark:border-sidebar-border p-4 shadow-sm space-y-2 block transition hover:bg-gray-100 dark:hover:bg-gray-800"
            >
                <img
                    v-if="course.photo"
                    :src="`/storage/${course.photo}`"
                    alt="Course Cover"
                    class="w-full h-40 object-cover rounded"
                />

                <h2 class="text-lg font-semibold text-rose-800">
                    {{ course.name }}
                </h2>
                
                <p class="text-gray-600 dark:text-gray-400">{{ course.description }}</p>
            </Link>
        </div>


    </AppLayout>
</template>
