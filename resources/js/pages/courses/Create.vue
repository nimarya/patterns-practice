<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
    { title: 'Courses', href: '/courses' },
    { title: 'Create', href: '/courses/create' },
];

const page = usePage();
const currentUserId = page.props.auth.user.id;

const name = ref('');
const description = ref('');
const type = ref('major_semester');

function submit() {
    router.post('/courses', {
        name: name.value,
        description: description.value,
        user_id: currentUserId,
        type: type.value,
    });
}
</script>

<template>
    <Head title="Create Course" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-xl mx-auto p-4 space-y-4">
            <h1 class="text-2xl font-bold mb-4">Create Course</h1>

            <input
                v-model="name"
                type="text"
                placeholder="Course Name"
                class="border rounded p-2 w-full"
            />

            <textarea
                v-model="description"
                placeholder="Description"
                rows="4"
                class="border rounded p-2 w-full"
            ></textarea>

            <select v-model="type" class="border rounded p-2 w-full">
                <option value="major_semester">Major Semester</option>
                <option value="major_year">Major Year</option>
                <option value="minor_semester">Minor Semester</option>
                <option value="minor_year">Minor Year</option>
            </select>

            <button @click="submit" class="bg-rose-800 text-white px-4 py-2 rounded-xl hover:bg-rose-600">
                Create Course
            </button>
        </div>
    </AppLayout>
</template>
