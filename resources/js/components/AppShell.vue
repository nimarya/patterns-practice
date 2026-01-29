<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { SharedData } from '@/types';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage<SharedData>().props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="relative min-h-screen w-full overflow-hidden bg-background text-foreground">
        <div class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-48 right-[-8%] h-[28rem] w-[28rem] rounded-full bg-emerald-200/50 blur-3xl dark:bg-emerald-500/15"
            ></div>
            <div
                class="absolute bottom-[-12rem] left-[-10%] h-[26rem] w-[26rem] rounded-full bg-emerald-100/70 blur-3xl dark:bg-emerald-400/10"
            ></div>
        </div>
        <div class="relative flex min-h-screen w-full flex-col">
            <slot />
        </div>
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <div class="min-h-screen w-full bg-background text-foreground">
            <slot />
        </div>
    </SidebarProvider>
</template>
