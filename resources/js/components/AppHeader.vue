<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, BookOpenCheck, BookOpen } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const currentPath = computed(() => page.url.split('?')[0]);
const isCurrentRoute = computed(() => (url: string) => currentPath.value === url);

const activeItemStyles = computed(() => (url: string) =>
    isCurrentRoute.value(url)
        ? 'bg-emerald-100 text-emerald-900 shadow-sm dark:bg-emerald-500/20 dark:text-emerald-100'
        : 'text-muted-foreground hover:text-foreground hover:bg-muted/70 dark:hover:bg-muted/40',
);

const mainNavItems: NavItem[] = [
    {
        title: 'My Courses',
        href: route('courses.mine'),
        icon: BookOpenCheck,
    },
    {
        title: 'Courses',
        href: route('courses.index'),
        icon: BookOpen,
    },
];
</script>

<template>
    <div>
        <header class="sticky top-0 z-40 w-full border-b border-border/70 bg-white/70 backdrop-blur dark:bg-slate-950/70">
            <div class="mx-auto flex h-16 max-w-6xl items-center gap-4 px-6 sm:px-8 lg:px-10">
                <div class="flex items-center gap-3">
                    <div class="lg:hidden">
                        <Sheet>
                            <SheetTrigger :as-child="true">
                                <Button variant="ghost" size="icon" class="h-9 w-9">
                                    <Menu class="h-5 w-5" />
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="left" class="w-[280px] p-6">
                                <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                                <SheetHeader class="flex justify-start text-left">
                                    <AppLogo />
                                </SheetHeader>
                                <div class="flex h-full flex-1 flex-col gap-6 py-6">
                                    <nav class="flex flex-col gap-1">
                                        <Link
                                            v-for="item in mainNavItems"
                                            :key="item.title"
                                            :href="item.href"
                                            class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition"
                                            :class="activeItemStyles(item.href)"
                                        >
                                            <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                                            {{ item.title }}
                                        </Link>
                                    </nav>
                                    <Button as-child class="w-full">
                                        <Link :href="route('courses.index')">Browse courses</Link>
                                    </Button>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>

                    <Link :href="route('dashboard')" class="flex items-center gap-2">
                        <AppLogo />
                    </Link>
                </div>

                <nav class="hidden items-center gap-2 lg:flex">
                    <Link
                        v-for="item in mainNavItems"
                        :key="item.title"
                        :href="item.href"
                        class="flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition"
                        :class="activeItemStyles(item.href)"
                    >
                        <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                        {{ item.title }}
                    </Link>
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <Button as-child variant="outline" size="sm" class="hidden sm:inline-flex">
                        <Link :href="route('courses.index')">Browse</Link>
                    </Button>
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-full bg-emerald-100 font-semibold text-emerald-900 dark:bg-emerald-500/30 dark:text-emerald-100">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </header>

        <div v-if="props.breadcrumbs.length > 1" class="border-b border-border/70">
            <div class="mx-auto flex h-11 w-full items-center px-6 text-sm text-muted-foreground sm:px-8 lg:max-w-6xl lg:px-10">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
