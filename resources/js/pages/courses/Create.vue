<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs = [
    { title: 'Courses', href: route('courses.index') },
    { title: 'Create', href: route('courses.create') },
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

    router.post(route('courses.store'), formData);
}
</script>

<template>
    <Head title="Create Course" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="mx-auto flex w-full max-w-2xl flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Create course</p>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">Craft a new learning path</h1>
                <p class="text-sm text-muted-foreground">Add a title, description, and pricing to publish a new course.</p>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <Label for="name">Course name</Label>
                    <Input id="name" v-model="name" type="text" placeholder="Modern Architecture Patterns" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="description"
                        rows="4"
                        placeholder="Describe what students will learn and the outcomes."
                        class="w-full rounded-2xl border border-border/70 bg-white px-4 py-3 text-sm text-foreground shadow-sm focus:border-emerald-200 focus:outline-none focus:ring-4 focus:ring-emerald-200/40 dark:bg-slate-950 dark:text-emerald-50"
                    ></textarea>
                </div>

                <div class="grid gap-2">
                    <Label for="price">Price (USD)</Label>
                    <Input id="price" v-model="price" type="number" step="0.01" min="0.01" placeholder="99.00" />
                </div>

                <div class="grid gap-2">
                    <Label for="type">Course type</Label>
                    <select
                        id="type"
                        v-model="type"
                        class="w-full rounded-2xl border border-border/70 bg-white px-4 py-3 text-sm text-foreground shadow-sm focus:border-emerald-200 focus:outline-none focus:ring-4 focus:ring-emerald-200/40 dark:bg-slate-950 dark:text-emerald-50"
                    >
                        <option value="major_semester">Major Semester</option>
                        <option value="major_year">Major Year</option>
                        <option value="minor_semester">Minor Semester</option>
                        <option value="minor_year">Minor Year</option>
                    </select>
                </div>

                <div class="grid gap-3">
                    <Label>Course cover</Label>
                    <label
                        class="relative flex cursor-pointer flex-col gap-3 rounded-2xl border border-dashed border-border/70 bg-white/60 p-6 text-center text-sm text-muted-foreground transition hover:border-emerald-200 dark:bg-slate-950/60"
                    >
                        <span>Upload a cover image for the course.</span>
                        <span class="text-xs">PNG or JPG · up to 5MB</span>
                        <div v-if="photo" class="flex flex-col gap-2 text-sm text-emerald-700 dark:text-emerald-300">
                            <span>File selected: {{ photo.name }}</span>
                            <img v-if="photoPreview" :src="photoPreview" class="h-32 rounded-2xl object-cover" />
                        </div>
                        <input type="file" @change="handleFileChange" class="absolute inset-0 h-full w-full opacity-0" />
                    </label>
                </div>

                <Button type="submit" class="w-full">Create course</Button>
            </form>
        </section>
    </AppLayout>
</template>
