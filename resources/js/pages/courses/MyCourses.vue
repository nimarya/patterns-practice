<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: '/courses' },
    { title: 'My Courses', href: '/my-courses' },
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
        <div class="p-4 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">My Courses</h1>
                <Link
                    href="/courses"
                    class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white"
                >
                    Browse all courses
                </Link>
            </div>

            <div
                v-if="showNotice"
                class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200"
            >
                Payment succeeded. Your course access will be granted shortly.
            </div>
        </div>

        <div v-if="props.courses.length === 0" class="p-6">
            <div class="rounded-xl border border-dashed border-gray-200 dark:border-sidebar-border p-6 text-center">
                <p class="text-gray-600 dark:text-gray-300">You haven’t purchased any courses yet.</p>
                <Link
                    href="/courses"
                    class="mt-4 inline-flex items-center rounded-full bg-rose-800 px-5 py-2 text-white hover:bg-rose-600"
                >
                    Browse courses
                </Link>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="course in props.courses"
                :key="course.id"
                :href="`/courses/${course.id}`"
                class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-sm transition hover:bg-emerald-50 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20"
            >
                <img
                    v-if="course.photo"
                    :src="`/storage/${course.photo}`"
                    alt="Course Cover"
                    class="w-full h-40 object-cover rounded"
                />

                <h2 class="mt-2 text-lg font-semibold text-rose-800">
                    {{ course.name }}
                </h2>

                <p class="text-gray-600 dark:text-gray-400">{{ course.description }}</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ formatPrice(course.price_cents, course.currency) }}
                </p>
            </Link>
        </div>
    </AppLayout>
</template>
