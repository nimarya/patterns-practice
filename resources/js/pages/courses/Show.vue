<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
    course: {
        id: number;
        name: string;
        description: string;
        lessons: {
            id: number;
            name: string;
            description: string;
        }[];
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: route('courses.index') },
    { title: props.course.name, href: route('courses.show', props.course.id) },
];
</script>

<template>
    <Head :title="props.course.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-col gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Course detail</p>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">{{ props.course.name }}</h1>
                <p class="max-w-2xl text-sm text-muted-foreground">{{ props.course.description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <Link
                    :href="route('courses.index')"
                    class="rounded-full border border-border/70 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                >
                    Back to catalog
                </Link>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="lesson in props.course.lessons"
                :key="lesson.id"
                class="rounded-3xl border border-border/60 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70"
            >
                <div class="flex flex-col gap-3">
                    <h3 class="text-lg font-semibold text-foreground">
                        <Link :href="route('lessons.show', lesson.id)" class="hover:text-emerald-700 dark:hover:text-emerald-300">
                            {{ lesson.name }}
                        </Link>
                    </h3>
                    <p class="text-sm text-muted-foreground">{{ lesson.description }}</p>
                    <Link
                        :href="route('lessons.show', lesson.id)"
                        class="text-sm font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-300"
                    >
                        Open lesson
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
