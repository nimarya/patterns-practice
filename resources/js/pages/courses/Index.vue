<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Courses', href: '/courses' },
];

const { courses, ownedCourseIds, authoredCourseIds, can } = defineProps<{
    courses: {
        id: number;
        name: string;
        description: string;
        photo: string;
        price_cents: number;
        currency: string;
    }[]
    ownedCourseIds: number[];
    authoredCourseIds: number[];
    can: {
        createCourse: boolean
    }
}>();

const ownedCourseSet = new Set(ownedCourseIds);
const authoredCourseSet = new Set(authoredCourseIds);

function isOwned(courseId: number) {
    return ownedCourseSet.has(courseId);
}

function isAuthored(courseId: number) {
    return authoredCourseSet.has(courseId);
}

function canPurchase(courseId: number) {
    return !isOwned(courseId) && !isAuthored(courseId);
}

function formatPrice(priceCents: number, currency: string) {
    const normalizedCurrency = currency?.toUpperCase() || 'USD';

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: normalizedCurrency,
    }).format(priceCents / 100);
}
</script>

<template>
    <Head title="Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="can.createCourse" class="flex justify-between items-center p-4">
            <h1 class="text-2xl font-bold">Courses</h1>
            <Link
                href="/courses/create"
                class="bg-rose-800 text-white px-4 py-2 rounded-xl hover:bg-rose-600"
            >
                Create Course
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="course in courses"
                :key="course.id"
                :class="[
                    'group relative rounded-xl border border-gray-200 dark:border-sidebar-border p-4 shadow-sm transition hover:bg-gray-100 dark:hover:bg-gray-800',
                    isAuthored(course.id)
                        ? 'border-sky-200 bg-sky-50/40 dark:bg-sky-500/10 dark:border-sky-500/30'
                        : '',
                    isOwned(course.id)
                        ? 'border-emerald-200 bg-emerald-50/40 dark:bg-emerald-500/10 dark:border-emerald-500/30'
                        : '',
                ]"
            >
                <span
                    v-if="isAuthored(course.id)"
                    class="absolute right-3 top-3 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800 dark:bg-sky-500/20 dark:text-sky-200"
                >
                    Your course
                </span>
                <span
                    v-else-if="isOwned(course.id)"
                    class="absolute right-3 top-3 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200"
                >
                    Owned
                </span>
                <Link :href="`/courses/${course.id}`" class="block space-y-2">
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
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ formatPrice(course.price_cents, course.currency) }}
                    </p>
                </Link>

                <div
                    v-if="canPurchase(course.id)"
                    class="absolute inset-0 rounded-xl bg-slate-900/50 opacity-0 transition flex items-center justify-center pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto"
                >
                    <Link
                        :href="`/courses/${course.id}/purchase`"
                        class="bg-rose-800 text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-rose-600"
                    >
                        Buy now
                    </Link>
                </div>
            </div>
        </div>


    </AppLayout>
</template>
