<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-background p-6 md:p-10">
        <div class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-40 right-[-15%] h-[26rem] w-[26rem] rounded-full bg-emerald-200/60 blur-3xl dark:bg-emerald-500/15"
            ></div>
            <div
                class="absolute bottom-[-12rem] left-[-10%] h-[24rem] w-[24rem] rounded-full bg-emerald-100/70 blur-3xl dark:bg-emerald-400/10"
            ></div>
        </div>
        <div class="relative w-full max-w-sm">
            <div class="flex flex-col gap-8 rounded-3xl border border-border/60 bg-white/85 p-8 shadow-lg shadow-emerald-100/30 backdrop-blur dark:bg-slate-950/80 dark:shadow-emerald-500/10">
                <div class="flex flex-col items-center gap-3 text-center">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm shadow-emerald-200/60 dark:bg-emerald-400 dark:text-emerald-950">
                            <AppLogoIcon class="size-6 fill-current" />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="flex flex-col gap-1">
                        <h1 class="text-2xl font-semibold text-foreground">{{ title }}</h1>
                        <p class="text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
