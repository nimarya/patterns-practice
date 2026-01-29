<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-background text-foreground">
        <header class="sticky top-0 z-40 w-full border-b border-border/70 bg-white/70 backdrop-blur dark:bg-slate-950/70">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6 sm:px-8 lg:px-10">
                <Link :href="route('home')" class="flex items-center gap-2">
                    <AppLogo />
                </Link>
                <nav class="flex items-center gap-2">
                    <template v-if="isAuthenticated">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-full border border-border/70 px-4 py-2 text-sm font-medium text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full border border-transparent px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-full border border-emerald-200 bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-200/50 transition hover:bg-emerald-600 dark:border-emerald-500/40 dark:shadow-emerald-500/10"
                        >
                            Sign up
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-6xl flex-col gap-16 px-6 pb-20 pt-12 sm:px-8 lg:px-10">
            <section class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700/80 dark:text-emerald-300/70">
                        <span class="rounded-full bg-emerald-100/70 px-3 py-1 dark:bg-emerald-500/20">Online school</span>
                        <span class="rounded-full bg-emerald-100/70 px-3 py-1 dark:bg-emerald-500/20">Backend patterns</span>
                        <span class="rounded-full bg-emerald-100/70 px-3 py-1 dark:bg-emerald-500/20">Practice labs</span>
                    </div>
                    <h1 class="text-4xl font-semibold leading-tight tracking-tight text-foreground sm:text-5xl">
                        Learn backend patterns through a calm, university-style online school.
                    </h1>
                    <p class="max-w-xl text-base text-muted-foreground">
                        Patterns Practice is a pet project for sharpening PHP architecture skills while simulating a real online school with teachers, students, and paid courses.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <template v-if="isAuthenticated">
                            <Link
                                :href="route('dashboard')"
                                class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10"
                            >
                                Open dashboard
                            </Link>
                            <Link
                                :href="route('courses.mine')"
                                class="rounded-full border border-border/70 px-6 py-3 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                            >
                                My courses
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('register')"
                                class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10"
                            >
                                Create account
                            </Link>
                            <Link
                                :href="route('login')"
                                class="rounded-full border border-border/70 px-6 py-3 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                            >
                                Log in
                            </Link>
                        </template>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                        <p class="text-sm uppercase tracking-[0.2em] text-emerald-600/70 dark:text-emerald-300/70">Campus view</p>
                        <p class="mt-4 text-lg font-semibold text-foreground">A focused space for students, teachers, and lesson flow.</p>
                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                                Teacher-made courses
                            </div>
                            <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                                Student enrollments
                            </div>
                            <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                                Lesson progress
                            </div>
                            <div class="rounded-2xl border border-emerald-100/80 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
                                Secure checkout
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-border/60 bg-white/70 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                        <p class="text-base text-foreground">
                            “Perfect sandbox for testing how an online university platform can work.”
                        </p>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20"></div>
                            <div class="flex flex-col text-sm">
                                <span class="font-medium text-foreground">Backend lab</span>
                                <span class="text-muted-foreground">nimaruya</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 md:grid-cols-3">
                <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70">
                    <h3 class="text-lg font-semibold text-foreground">Student journeys</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Enroll, purchase, and complete lessons in a clear flow.</p>
                </div>
                <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70">
                    <h3 class="text-lg font-semibold text-foreground">Teacher workspace</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Create courses, manage curricula, and ship new content.</p>
                </div>
                <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70">
                    <h3 class="text-lg font-semibold text-foreground">Payment ready</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Simulated checkout to practice real-world backend flows.</p>
                </div>
            </section>
        </main>
    </div>
</template>
