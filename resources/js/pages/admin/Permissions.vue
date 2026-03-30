<script setup lang="ts">
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Plus, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';
import type { BreadcrumbItem, SharedData } from '@/types';

interface PermissionItem {
    id: number;
    name: string;
}

interface RoleOverview {
    id: number;
    name: string;
    users_count: number;
    permissions: string[];
}

interface Props {
    permissions: PermissionItem[];
    roles: RoleOverview[];
}

const props = defineProps<Props>();
const page = usePage<SharedData>();
const canViewUsersSettings = page.props.auth.canSettingsUsers;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: canViewUsersSettings ? route('admin.users.index') : route('admin.permissions.index'),
    },
    {
        title: 'Permissions',
        href: route('admin.permissions.index'),
    },
];

const addPermissionDialogOpen = ref(false);
const editPermissionDialogOpen = ref(false);
const addRoleDialogOpen = ref(false);
const roleDialogOpen = ref(false);

const selectedRole = ref<RoleOverview | null>(null);
const selectedPermission = ref<PermissionItem | null>(null);

const permissionForm = useForm({
    name: '',
});

const editPermissionForm = useForm({
    id: null as number | null,
    name: '',
});

const roleForm = useForm({
    name: '',
    permissions: [] as string[],
});

const rolePermissionsForm = useForm({
    permissions: [] as string[],
});

const closeAddPermissionDialog = (): void => {
    addPermissionDialogOpen.value = false;
    permissionForm.reset();
    permissionForm.clearErrors();
};

const closeEditPermissionDialog = (): void => {
    editPermissionDialogOpen.value = false;
    selectedPermission.value = null;
    editPermissionForm.reset();
    editPermissionForm.clearErrors();
};

const closeAddRoleDialog = (): void => {
    addRoleDialogOpen.value = false;
    roleForm.reset();
    roleForm.clearErrors();
};

const closeRoleDialog = (): void => {
    roleDialogOpen.value = false;
    selectedRole.value = null;
    rolePermissionsForm.reset();
    rolePermissionsForm.clearErrors();
};

const submitPermission = (): void => {
    permissionForm.post(route('admin.permissions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddPermissionDialog();
        },
    });
};

const openEditPermissionDialog = (permission: PermissionItem): void => {
    selectedPermission.value = permission;
    editPermissionForm.id = permission.id;
    editPermissionForm.name = permission.name;
    editPermissionForm.clearErrors();
    editPermissionDialogOpen.value = true;
};

const submitPermissionUpdate = (): void => {
    if (!editPermissionForm.id) {
        return;
    }

    editPermissionForm.put(route('admin.permissions.update', editPermissionForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditPermissionDialog();
        },
    });
};

const submitRole = (): void => {
    roleForm.permissions = [...new Set(roleForm.permissions)].filter((permission) => permission.length > 0);

    roleForm.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddRoleDialog();
        },
    });
};

const openRoleDialog = (role: RoleOverview): void => {
    selectedRole.value = role;
    rolePermissionsForm.permissions = [...role.permissions];
    rolePermissionsForm.clearErrors();
    roleDialogOpen.value = true;
};

const submitRolePermissions = (): void => {
    if (!selectedRole.value) {
        return;
    }

    rolePermissionsForm.put(route('admin.roles.permissions.update', selectedRole.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeRoleDialog();
        },
    });
};

const deletePermission = (): void => {
    if (!selectedPermission.value) {
        return;
    }

    if (!window.confirm('Delete this permission?')) {
        return;
    }

    router.delete(route('admin.permissions.destroy', selectedPermission.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditPermissionDialog();
        },
    });
};

const deleteRole = (): void => {
    if (!selectedRole.value) {
        return;
    }

    if (!window.confirm('Delete this role?')) {
        return;
    }

    router.delete(route('admin.roles.destroy', selectedRole.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeRoleDialog();
        },
    });
};
</script>

<template>
    <Head title="Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="flex flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Admin settings</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">Permissions</h1>
                    <p class="text-sm text-muted-foreground">Overview of roles and permissions available in the system.</p>
                </div>
                <Link
                    v-if="canViewUsersSettings"
                    :href="route('admin.users.index')"
                    class="rounded-full border border-border/70 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:text-emerald-100"
                >
                    Manage users
                </Link>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-emerald-100 p-2 text-emerald-900 dark:bg-emerald-500/20 dark:text-emerald-100">
                            <ShieldCheck class="h-5 w-5" />
                        </div>
                        <h2 class="text-lg font-semibold text-foreground">System permissions</h2>
                    </div>

                    <Dialog v-model:open="addPermissionDialogOpen">
                        <DialogTrigger as-child>
                            <Button variant="outline" class="rounded-full">
                                <Plus class="h-4 w-4" />
                                Add permission
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-lg">
                            <form class="flex flex-col gap-6" @submit.prevent="submitPermission">
                                <DialogHeader class="space-y-2">
                                    <DialogTitle>Add permission</DialogTitle>
                                    <DialogDescription>Create a new system permission.</DialogDescription>
                                </DialogHeader>

                                <div class="grid gap-2">
                                    <Label for="permission-name">Permission name</Label>
                                    <Input id="permission-name" v-model="permissionForm.name" placeholder="users.manage" />
                                    <InputError :message="permissionForm.errors.name" />
                                </div>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button type="button" variant="secondary" @click="closeAddPermissionDialog">Cancel</Button>
                                    </DialogClose>
                                    <Button type="submit" :disabled="permissionForm.processing">Create</Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="permission in props.permissions"
                        :key="permission.id"
                        type="button"
                        class="rounded-full border border-border/80 bg-muted/40 px-3 py-1 text-xs font-medium text-foreground transition hover:border-emerald-200 hover:text-emerald-900 dark:hover:border-emerald-500/30 dark:hover:text-emerald-200"
                        @click="openEditPermissionDialog(permission)"
                    >
                        {{ permission.name }}
                    </button>
                    <p v-if="!props.permissions.length" class="text-sm text-muted-foreground">No permissions are registered in the system yet.</p>
                </div>
            </div>

            <div class="rounded-3xl border border-border/60 bg-white/80 p-6 shadow-sm backdrop-blur dark:bg-slate-950/70">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-foreground">Roles overview</h2>

                    <Dialog v-model:open="addRoleDialogOpen">
                        <DialogTrigger as-child>
                            <Button variant="outline" class="rounded-full">
                                <Plus class="h-4 w-4" />
                                Add role
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-lg">
                            <form class="flex flex-col gap-6" @submit.prevent="submitRole">
                                <DialogHeader class="space-y-2">
                                    <DialogTitle>Add role</DialogTitle>
                                    <DialogDescription>Create a new role that can be assigned to users.</DialogDescription>
                                </DialogHeader>

                                <div class="grid gap-2">
                                    <Label for="role-name">Role name</Label>
                                    <Input id="role-name" v-model="roleForm.name" placeholder="manager" />
                                    <InputError :message="roleForm.errors.name" />
                                </div>

                                <div class="grid gap-3">
                                    <p class="text-sm font-medium text-foreground">Permissions</p>
                                    <div class="max-h-[240px] overflow-y-auto rounded-2xl border border-border/70 p-3">
                                        <div v-if="props.permissions.length" class="grid gap-3">
                                            <label
                                                v-for="permission in props.permissions"
                                                :key="permission.id"
                                                class="flex items-center gap-3 rounded-xl border border-border/70 bg-muted/20 px-3 py-2"
                                            >
                                                <input
                                                    v-model="roleForm.permissions"
                                                    type="checkbox"
                                                    :value="permission.name"
                                                    class="size-4 rounded border border-input"
                                                />
                                                <span class="text-sm text-foreground">{{ permission.name }}</span>
                                            </label>
                                        </div>
                                        <p v-else class="text-sm text-muted-foreground">No permissions available yet.</p>
                                    </div>
                                    <InputError :message="roleForm.errors.permissions" />
                                </div>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button type="button" variant="secondary" @click="closeAddRoleDialog">Cancel</Button>
                                    </DialogClose>
                                    <Button type="submit" :disabled="roleForm.processing">Create</Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <div class="mt-4 flex flex-col gap-3">
                    <button
                        v-for="role in props.roles"
                        :key="role.id"
                        type="button"
                        class="rounded-2xl border border-border/70 bg-muted/20 p-4 text-left transition hover:border-emerald-200 hover:bg-emerald-50/30 dark:hover:border-emerald-500/30 dark:hover:bg-emerald-500/10"
                        @click="openRoleDialog(role)"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-foreground">{{ role.name }}</p>
                            <span class="text-xs text-muted-foreground">{{ role.users_count }} users</span>
                        </div>
                        <p class="mt-2 text-xs text-muted-foreground">{{ role.permissions.length }} permissions assigned</p>
                    </button>
                    <p v-if="!props.roles.length" class="text-sm text-muted-foreground">No roles have been created yet.</p>
                </div>
            </div>
        </section>

        <Dialog v-model:open="editPermissionDialogOpen">
            <DialogContent class="sm:max-w-lg">
                <form class="flex flex-col gap-6" @submit.prevent="submitPermissionUpdate">
                    <DialogHeader class="space-y-2">
                        <DialogTitle>Edit permission</DialogTitle>
                        <DialogDescription>Update the selected permission name.</DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-permission-name">Permission name</Label>
                        <Input id="edit-permission-name" v-model="editPermissionForm.name" placeholder="users.manage" />
                        <InputError :message="editPermissionForm.errors.name" />
                    </div>

                    <DialogFooter class="justify-end gap-2">
                        <Button type="button" variant="destructive" :disabled="editPermissionForm.processing" @click="deletePermission">Delete</Button>
                        <DialogClose as-child>
                            <Button type="button" variant="secondary" @click="closeEditPermissionDialog">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="editPermissionForm.processing">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="roleDialogOpen">
            <DialogContent class="sm:max-w-2xl">
                <form class="flex flex-col gap-6" @submit.prevent="submitRolePermissions">
                    <DialogHeader class="space-y-2">
                        <DialogTitle>Role permissions: {{ selectedRole?.name ?? 'Role' }}</DialogTitle>
                        <DialogDescription>Select which permissions should be assigned to this role.</DialogDescription>
                    </DialogHeader>

                    <div class="max-h-[360px] overflow-y-auto rounded-2xl border border-border/70 p-4">
                        <div v-if="props.permissions.length" class="grid gap-3 sm:grid-cols-2">
                            <label
                                v-for="permission in props.permissions"
                                :key="permission.id"
                                class="flex items-center gap-3 rounded-xl border border-border/70 bg-muted/20 px-3 py-2"
                            >
                                <input
                                    v-model="rolePermissionsForm.permissions"
                                    type="checkbox"
                                    :value="permission.name"
                                    class="size-4 rounded border border-input"
                                />
                                <span class="text-sm text-foreground">{{ permission.name }}</span>
                            </label>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">Create at least one permission to configure roles.</p>
                    </div>

                    <InputError :message="rolePermissionsForm.errors.permissions" />

                    <DialogFooter class="justify-end gap-2">
                        <Button type="button" variant="destructive" :disabled="rolePermissionsForm.processing" @click="deleteRole">Delete</Button>
                        <DialogClose as-child>
                            <Button type="button" variant="secondary" @click="closeRoleDialog">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="rolePermissionsForm.processing">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
