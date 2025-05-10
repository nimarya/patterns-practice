<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    course: {
        id: number;
        name: string;
        description: string;
        modules: {
            id: number;
            name: string;
            description: string;
        }[];
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Courses',
        href: '/courses',
    },
    {
        title: props.course.name,
        href: '',
    },
];
</script>

<template>
    <Head :title="props.course.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-2">{{ props.course.name }}</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">{{ props.course.description }}</p>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="module in props.course.modules"
                    :key="module.id"
                    class="rounded-xl border border-gray-200 dark:border-sidebar-border p-4 shadow-sm"
                >
                    <h3 class="text-lg font-semibold mb-2">{{ module.name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ module.description }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
