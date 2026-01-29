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
    { title: 'Courses', href: route('courses.index') },
    { title: props.lesson.course.name, href: route('courses.show', props.lesson.course.id) },
    { title: props.lesson.name, href: route('lessons.show', props.lesson.id) },
];
</script>

<template>
    <Head :title="props.lesson.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-col gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Lesson</p>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">{{ props.lesson.name }}</h1>
                <p class="max-w-2xl text-sm text-muted-foreground">{{ props.lesson.description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <Link
                    :href="route('courses.show', props.lesson.course.id)"
                    class="rounded-full border border-border/70 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                >
                    Back to course
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
