<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-44 left-[-10%] h-[24rem] w-[24rem] rounded-full bg-emerald-100/80 blur-3xl dark:bg-emerald-400/10"></div>
            <div class="absolute bottom-[-12rem] right-[-15%] h-[26rem] w-[26rem] rounded-full bg-emerald-200/50 blur-3xl dark:bg-emerald-500/15"></div>
        </div>
        <div class="relative flex w-full max-w-md flex-col gap-6">
            <Link :href="route('home')" class="flex items-center gap-2 self-center font-medium">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm shadow-emerald-200/60 dark:bg-emerald-400 dark:text-emerald-950">
                    <AppLogoIcon class="size-6 fill-current" />
                </div>
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-3xl border-border/60 bg-white/85 shadow-lg shadow-emerald-100/30 backdrop-blur dark:bg-slate-950/80 dark:shadow-emerald-500/10">
                    <CardHeader class="px-10 pb-0 pt-8 text-center">
                        <CardTitle class="text-2xl">{{ title }}</CardTitle>
                        <CardDescription>
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
