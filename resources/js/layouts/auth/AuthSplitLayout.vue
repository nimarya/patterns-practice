<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-svh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col overflow-hidden bg-emerald-950 p-10 text-white lg:flex">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.35),transparent_55%)]" />
            <div class="relative z-20 flex items-center gap-3 text-lg font-semibold">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10">
                    <AppLogoIcon class="size-6 fill-current text-white" />
                </div>
                {{ name }}
            </div>
            <div class="relative z-20 mt-auto flex flex-col gap-4">
                <p class="text-sm uppercase tracking-[0.2em] text-emerald-200/70">Patterns practice</p>
                <p class="text-2xl font-semibold leading-tight">Build learning paths with clean patterns and fast feedback.</p>
                <div v-if="quote" class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <blockquote class="flex flex-col gap-2">
                        <p class="text-base">&ldquo;{{ quote.message }}&rdquo;</p>
                        <footer class="text-sm text-emerald-200/70">{{ quote.author }}</footer>
                    </blockquote>
                </div>
            </div>
        </div>
        <div class="relative flex w-full items-center justify-center bg-background p-6 lg:p-10">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -top-40 right-[-15%] h-[22rem] w-[22rem] rounded-full bg-emerald-100/70 blur-3xl dark:bg-emerald-500/10"></div>
            </div>
            <div class="relative mx-auto flex w-full flex-col gap-6 sm:w-[360px]">
                <div class="flex flex-col gap-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight text-foreground" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
