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
const photo = ref<File | null>(null);
const photoPreview = ref<string | null>(null);
const price = ref('');

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        photo.value = target.files[0];
        photoPreview.value = URL.createObjectURL(target.files[0]);
    }
}

function submit() {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('description', description.value);
    formData.append('user_id', currentUserId);
    formData.append('type', type.value);
    formData.append('price', price.value);
    formData.append('currency', 'usd');

    if (photo.value) {
        formData.append('photo', photo.value);
    }

    router.post('/courses', formData);
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

            <input
                v-model="price"
                type="number"
                step="0.01"
                min="0.01"
                placeholder="Price (USD)"
                class="border rounded p-2 w-full"
            />

            <select v-model="type" class="border rounded p-2 w-full">
                <option value="major_semester">Major Semester</option>
                <option value="major_year">Major Year</option>
                <option value="minor_semester">Minor Semester</option>
                <option value="minor_year">Minor Year</option>
            </select>

            <div
                class="p-4 border-dashed border-2 border-gray-300 rounded-xl text-center cursor-pointer hover:border-rose-500 relative"
            >
                <p class="mb-2">Upload Course Cover</p>

                <div v-if="photo" class="flex flex-col items-center mb-2">
                    <p class="text-rose-800 font-medium">File selected: {{ photo.name }}</p>
                    <img v-if="photoPreview" :src="photoPreview" class="h-32 object-contain rounded mt-2" />
                </div>

                <input
                    type="file"
                    @change="handleFileChange"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />
            </div>


            <button @click="submit" class="bg-rose-800 text-white px-4 py-2 rounded-xl hover:bg-rose-600">
                Create Course
            </button>
        </div>
    </AppLayout>
</template>
