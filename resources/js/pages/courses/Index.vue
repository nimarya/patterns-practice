<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Courses', href: route('courses.index') },
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
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Course catalog</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">Courses</h1>
                    <p class="text-sm text-muted-foreground">Browse clean, structured patterns for focused learning.</p>
                </div>
                <Link
                    v-if="can.createCourse"
                    :href="route('courses.create')"
                    class="rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10"
                >
                    Create course
                </Link>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="course in courses"
                :key="course.id"
                class="group relative flex h-full flex-col overflow-hidden rounded-3xl border border-border/60 bg-white/80 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70"
            >
                <div class="relative h-44 overflow-hidden">
                    <img
                        v-if="course.photo"
                        :src="`/storage/${course.photo}`"
                        alt="Course Cover"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center bg-emerald-50 text-sm text-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-100">
                        No cover yet
                    </div>
                    <span
                        v-if="isAuthored(course.id)"
                        class="absolute right-4 top-4 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100"
                    >
                        Your course
                    </span>
                    <span
                        v-else-if="isOwned(course.id)"
                        class="absolute right-4 top-4 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100"
                    >
                        Owned
                    </span>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-5">
                    <h2 class="text-lg font-semibold text-foreground">{{ course.name }}</h2>
                    <p class="text-sm text-muted-foreground">{{ course.description }}</p>
                    <div class="mt-auto flex items-center justify-between text-sm">
                        <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                            {{ formatPrice(course.price_cents, course.currency) }}
                        </span>
                        <Link
                            :href="route('courses.show', course.id)"
                            class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-300"
                        >
                            View details
                        </Link>
                    </div>
                </div>

                <div
                    v-if="canPurchase(course.id)"
                    class="absolute inset-0 flex items-center justify-center bg-slate-950/50 opacity-0 transition group-hover:opacity-100"
                >
                    <Link
                        :href="route('courses.purchase.show', course.id)"
                        class="rounded-full bg-white px-6 py-2 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-50"
                    >
                        Buy now
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
