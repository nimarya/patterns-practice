<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Studio dashboard</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">Welcome back to your learning studio.</h1>
                    <p class="max-w-xl text-sm text-muted-foreground">
                        Track progress, revisit materials, and manage your courses with a calm, card-first interface.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link
                        :href="route('courses.index')"
                        class="rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10"
                    >
                        Explore courses
                    </Link>
                    <Link
                        :href="route('courses.mine')"
                        class="rounded-full border border-border/70 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                    >
                        My courses
                    </Link>
                </div>
            </div>
        </section>

        <section class="grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <p class="text-sm text-muted-foreground">In progress</p>
                <p class="mt-3 text-2xl font-semibold text-foreground">4 courses</p>
                <p class="mt-2 text-sm text-muted-foreground">Keep a steady weekly cadence.</p>
            </div>
            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <p class="text-sm text-muted-foreground">Lessons completed</p>
                <p class="mt-3 text-2xl font-semibold text-foreground">26 lessons</p>
                <p class="mt-2 text-sm text-muted-foreground">Consistency builds mastery.</p>
            </div>
            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <p class="text-sm text-muted-foreground">Momentum</p>
                <p class="mt-3 text-2xl font-semibold text-foreground">+12% week</p>
                <p class="mt-2 text-sm text-muted-foreground">Stay in the green zone.</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col gap-2">
                        <h2 class="text-xl font-semibold text-foreground">Recent activity</h2>
                        <p class="text-sm text-muted-foreground">Your last sessions, all in one glance.</p>
                    </div>
                    <Link :href="route('courses.index')" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-300">
                        View all
                    </Link>
                </div>
                <div class="mt-6 grid gap-4">
                    <div class="flex items-center justify-between rounded-2xl border border-border/60 bg-white/70 px-4 py-3 text-sm dark:bg-slate-950/60">
                        <div class="flex flex-col">
                            <span class="font-medium text-foreground">Architecture Patterns</span>
                            <span class="text-muted-foreground">Lesson 5 · Completed 2 hours ago</span>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100">
                            Completed
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-border/60 bg-white/70 px-4 py-3 text-sm dark:bg-slate-950/60">
                        <div class="flex flex-col">
                            <span class="font-medium text-foreground">Domain Driven Design</span>
                            <span class="text-muted-foreground">Lesson 3 · In progress</span>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100">
                            Active
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-border/60 bg-white/70 px-4 py-3 text-sm dark:bg-slate-950/60">
                        <div class="flex flex-col">
                            <span class="font-medium text-foreground">API Design Systems</span>
                            <span class="text-muted-foreground">Lesson 1 · Up next</span>
                        </div>
                        <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-foreground">
                            Scheduled
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <h3 class="text-lg font-semibold text-foreground">Today’s focus</h3>
                <p class="mt-2 text-sm text-muted-foreground">Plan a single, high-impact session.</p>
                <div class="mt-6 flex flex-col gap-3">
                    <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                        Review your current module notes.
                    </div>
                    <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                        Finish one lesson without distractions.
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
