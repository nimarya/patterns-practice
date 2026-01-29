<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: route('courses.index') },
    { title: 'My Courses', href: route('courses.mine') },
];

const props = defineProps<{
    courses: {
        id: number;
        name: string;
        description: string;
        photo: string;
        price_cents: number;
        currency: string;
    }[];
    notice?: string | null;
}>();

const showNotice = ref(props.notice === 'access-pending');

function formatPrice(priceCents: number, currency: string) {
    const normalizedCurrency = currency?.toUpperCase() || 'USD';

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: normalizedCurrency,
    }).format(priceCents / 100);
}

onMounted(() => {
    if (!showNotice.value) {
        return;
    }

    router.visit(route('courses.mine'), {
        replace: true,
        preserveScroll: true,
        preserveState: true,
    });

    window.setTimeout(() => {
        showNotice.value = false;
    }, 6000);
});
</script>

<template>
    <Head title="My Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">My library</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">My Courses</h1>
                    <p class="text-sm text-muted-foreground">Jump back into your active learning paths.</p>
                </div>
                <Link
                    :href="route('courses.index')"
                    class="rounded-full border border-border/70 px-5 py-2 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                >
                    Browse all courses
                </Link>
            </div>

            <div
                v-if="showNotice"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100"
            >
                Payment succeeded. Your course access will be granted shortly.
            </div>
        </section>

        <section v-if="props.courses.length === 0" class="rounded-3xl border border-dashed border-border/70 bg-white/60 p-10 text-center shadow-sm backdrop-blur dark:bg-slate-950/60">
            <p class="text-sm text-muted-foreground">You haven’t purchased any courses yet.</p>
            <Link
                :href="route('courses.index')"
                class="mt-6 inline-flex items-center rounded-full bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10"
            >
                Browse courses
            </Link>
        </section>

        <section v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="course in props.courses"
                :key="course.id"
                :href="route('courses.show', course.id)"
                class="group flex h-full flex-col overflow-hidden rounded-3xl border border-border/60 bg-white/80 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-950/70"
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
                </div>
                <div class="flex flex-1 flex-col gap-3 p-5">
                    <h2 class="text-lg font-semibold text-foreground">{{ course.name }}</h2>
                    <p class="text-sm text-muted-foreground">{{ course.description }}</p>
                    <div class="mt-auto flex items-center justify-between text-sm">
                        <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                            {{ formatPrice(course.price_cents, course.currency) }}
                        </span>
                        <span class="text-xs font-medium text-muted-foreground">Continue</span>
                    </div>
                </div>
            </Link>
        </section>
    </AppLayout>
</template>
