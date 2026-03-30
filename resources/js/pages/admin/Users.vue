<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ArrowUpDown, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { BreadcrumbItem, SharedData } from '@/types';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    created_at: string | null;
    roles: string[];
}

interface Props {
    users: AdminUser[];
    roles: string[];
}

type SortColumn = 'name' | 'role' | 'registered';
type SortDirection = 'asc' | 'desc';

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: route('admin.users.index'),
    },
    {
        title: 'Users',
        href: route('admin.users.index'),
    },
];

const page = usePage<SharedData & { errors?: Record<string, string> }>();

const createDialogOpen = ref(false);
const editDialogOpen = ref(false);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
});

const editForm = useForm({
    id: null as number | null,
    name: '',
    email: '',
    password: '',
    role: '',
});

const pageErrors = computed(() => page.props.errors ?? {});

const sortColumn = ref<SortColumn>('registered');
const sortDirection = ref<SortDirection>('desc');

const userPrimaryRole = (user: AdminUser): string => user.roles[0] ?? '';

const formattedUsers = computed(() =>
    [...props.users].sort((firstUser, secondUser) => {
        let comparison = 0;

        if (sortColumn.value === 'name') {
            comparison = firstUser.name.localeCompare(secondUser.name, undefined, { sensitivity: 'base' });
        }

        if (sortColumn.value === 'role') {
            comparison = userPrimaryRole(firstUser).localeCompare(userPrimaryRole(secondUser), undefined, { sensitivity: 'base' });
        }

        if (sortColumn.value === 'registered') {
            const firstUserDate = firstUser.created_at ? new Date(firstUser.created_at).getTime() : 0;
            const secondUserDate = secondUser.created_at ? new Date(secondUser.created_at).getTime() : 0;
            comparison = firstUserDate - secondUserDate;
        }

        if (comparison === 0) {
            comparison = firstUser.id - secondUser.id;
        }

        return sortDirection.value === 'asc' ? comparison : -comparison;
    }),
);

const roleOptions = computed(() => props.roles);

const isCurrentUser = (userId: number): boolean => page.props.auth.user.id === userId;

const formatDate = (date: string | null): string => {
    if (!date) {
        return 'N/A';
    }

    return new Intl.DateTimeFormat('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(date));
};

const closeCreateDialog = (): void => {
    createDialogOpen.value = false;
    createForm.reset();
    createForm.clearErrors();
};

const closeEditDialog = (): void => {
    editDialogOpen.value = false;
    editForm.reset();
    editForm.clearErrors();
};

const openEditDialog = (user: AdminUser): void => {
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.role = user.roles[0] ?? '';
    editDialogOpen.value = true;
};

const submitCreate = (): void => {
    createForm.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeCreateDialog();
        },
    });
};

const submitUpdate = (): void => {
    if (!editForm.id) {
        return;
    }

    editForm.put(route('admin.users.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditDialog();
        },
    });
};

const removeUser = (user: AdminUser): void => {
    router.delete(route('admin.users.destroy', user.id), {
        preserveScroll: true,
    });
};

const isSortedBy = (column: SortColumn): boolean => sortColumn.value === column;

const setSort = (column: SortColumn): void => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        return;
    }

    sortColumn.value = column;
    sortDirection.value = column === 'registered' ? 'desc' : 'asc';
};

const sortIcon = (column: SortColumn) => {
    if (!isSortedBy(column)) {
        return ArrowUpDown;
    }

    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Admin settings</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">Users</h1>
                    <p class="text-sm text-muted-foreground">Manage users and their roles in one place.</p>
                </div>

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button class="rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-200/60 transition hover:bg-emerald-600 dark:shadow-emerald-500/10">
                            <Plus class="h-4 w-4" />
                            Add user
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-lg">
                        <form class="flex flex-col gap-6" @submit.prevent="submitCreate">
                            <DialogHeader class="space-y-2">
                                <DialogTitle>Create user</DialogTitle>
                                <DialogDescription>Create a new user and assign a role if needed.</DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-4">
                                <div class="grid gap-2">
                                    <Label for="create-name">Name</Label>
                                    <Input id="create-name" v-model="createForm.name" placeholder="Jane Doe" />
                                    <InputError :message="createForm.errors.name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="create-email">Email</Label>
                                    <Input id="create-email" type="email" v-model="createForm.email" placeholder="jane@example.com" />
                                    <InputError :message="createForm.errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="create-password">Password</Label>
                                    <Input id="create-password" type="password" v-model="createForm.password" placeholder="Minimum 8 symbols" />
                                    <InputError :message="createForm.errors.password" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="create-role">Role</Label>
                                    <select
                                        id="create-role"
                                        v-model="createForm.role"
                                        class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    >
                                        <option value="">No role</option>
                                        <option v-for="role in roleOptions" :key="role" :value="role">{{ role }}</option>
                                    </select>
                                    <InputError :message="createForm.errors.role" />
                                </div>
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary" type="button" @click="closeCreateDialog">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="createForm.processing">Create</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <div v-if="pageErrors.delete_user" class="rounded-2xl border border-destructive/20 bg-destructive/5 px-4 py-3 text-sm text-destructive">
                {{ pageErrors.delete_user }}
            </div>
        </section>

        <section class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="overflow-x-auto rounded-2xl border border-border/70">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-muted/40 text-xs uppercase tracking-[0.12em] text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-semibold">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 transition hover:text-foreground"
                                    @click="setSort('name')"
                                >
                                    Name
                                    <component :is="sortIcon('name')" class="h-3.5 w-3.5" />
                                </button>
                            </th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 transition hover:text-foreground"
                                    @click="setSort('role')"
                                >
                                    Role
                                    <component :is="sortIcon('role')" class="h-3.5 w-3.5" />
                                </button>
                            </th>
                            <th class="px-4 py-3 font-semibold">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 transition hover:text-foreground"
                                    @click="setSort('registered')"
                                >
                                    Registered
                                    <component :is="sortIcon('registered')" class="h-3.5 w-3.5" />
                                </button>
                            </th>
                            <th class="px-4 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in formattedUsers"
                            :key="user.id"
                            class="border-t border-border/60 transition hover:bg-muted/20"
                        >
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="font-medium text-foreground">{{ user.name }}</span>
                                    <span v-if="isCurrentUser(user.id)" class="text-xs text-emerald-700 dark:text-emerald-300">Current account</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <span
                                    v-if="user.roles.length"
                                    class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100"
                                >
                                    {{ user.roles[0] }}
                                </span>
                                <span v-else class="text-muted-foreground">No role</span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ formatDate(user.created_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" class="rounded-full" type="button" @click="openEditDialog(user)">
                                        <Pencil class="h-4 w-4" />
                                        Edit
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        class="rounded-full"
                                        type="button"
                                        :disabled="isCurrentUser(user.id)"
                                        @click="removeUser(user)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!formattedUsers.length">
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-muted-foreground">No users are registered yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <Dialog v-model:open="editDialogOpen">
            <DialogContent class="sm:max-w-lg">
                <form class="flex flex-col gap-6" @submit.prevent="submitUpdate">
                    <DialogHeader class="space-y-2">
                        <DialogTitle>Edit user</DialogTitle>
                        <DialogDescription>Update user details and role if needed.</DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="edit-name">Name</Label>
                            <Input id="edit-name" v-model="editForm.name" placeholder="Jane Doe" />
                            <InputError :message="editForm.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="edit-email">Email</Label>
                            <Input id="edit-email" type="email" v-model="editForm.email" placeholder="jane@example.com" />
                            <InputError :message="editForm.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="edit-password">New password</Label>
                            <Input id="edit-password" type="password" v-model="editForm.password" placeholder="Leave empty to keep unchanged" />
                            <InputError :message="editForm.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="edit-role">Role</Label>
                            <select
                                id="edit-role"
                                v-model="editForm.role"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">No role</option>
                                <option v-for="role in roleOptions" :key="role" :value="role">{{ role }}</option>
                            </select>
                            <InputError :message="editForm.errors.role" />
                        </div>
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary" type="button" @click="closeEditDialog">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="editForm.processing">Save changes</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
