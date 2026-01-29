<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-3">
        <div
            class="flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm shadow-emerald-200/60 dark:bg-emerald-400 dark:text-emerald-950 dark:shadow-emerald-500/10"
        >
            <AppLogoIcon class="size-6 fill-current" />
        </div>
        <div class="flex flex-col leading-tight">
            <span class="text-sm font-semibold tracking-tight text-foreground">nimaruya</span>
            <span class="text-xs text-muted-foreground">online school</span>
        </div>
    </div>
</template>
