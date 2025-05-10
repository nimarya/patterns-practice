<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Courses',
        href: '/courses',
    },
];

defineProps<{
    courses: {
        id: number;
        name: string;
        description: string;
    }[]
}>();
</script>

<template>
    <Head title="Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="course in courses"
                :key="course.id"
                class="rounded-xl border border-gray-200 dark:border-sidebar-border p-4 shadow-sm"
            >
                <h2 class="text-lg font-semibold mb-2">
                    <Link :href="`/courses/${course.id}`" class="text-rose-800 hover:text-rose-600">
                        {{ course.name }}
                    </Link>
                </h2>
                <p class="text-gray-600 dark:text-gray-400">{{ course.description }}</p>
            </div>
        </div>
    </AppLayout>
</template>
