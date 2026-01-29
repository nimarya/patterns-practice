<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: '/settings/profile',
    },
    {
        title: 'Password',
        href: '/settings/password',
    },
    {
        title: 'Appearance',
        href: '/settings/appearance',
    },
];

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col gap-8 lg:flex-row lg:gap-12">
            <aside class="w-full max-w-xl lg:w-56">
                <nav class="flex flex-col gap-2">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="[
                            'w-full justify-start rounded-2xl px-4 py-2 text-sm font-medium transition',
                            currentPath === item.href
                                ? 'bg-emerald-100 text-emerald-900 shadow-sm dark:bg-emerald-500/20 dark:text-emerald-100'
                                : 'text-muted-foreground hover:bg-muted/70 hover:text-foreground dark:hover:bg-muted/40',
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="flex flex-col gap-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
