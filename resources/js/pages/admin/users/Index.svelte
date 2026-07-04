<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'User Management', href: '/admin/users' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { users = [], roles = [], flash = {} }: { users: any[]; roles: any[]; flash: any } = $props();

    let showCreateModal = $state(false);
    let showEditModal = $state(false);
    let selectedUser = $state<any>(null);

    // Form inputs
    let username = $state('');
    let name = $state('');
    let email = $state('');
    let phone = $state('');
    let password = $state('');
    let role_id = $state('');

    function resetForm() {
        username = '';
        name = '';
        email = '';
        phone = '';
        password = '';
        role_id = roles[0]?.id || '';
    }

    function openCreate() {
        resetForm();
        showCreateModal = true;
    }

    function openEdit(user: any) {
        selectedUser = user;
        username = user.username;
        name = user.name;
        email = user.email;
        phone = user.phone;
        role_id = roles.find(r => r.name === user.role)?.id || '';
        password = '';
        showEditModal = true;
    }

    function submitCreate() {
        router.post('/admin/users', {
            username,
            name,
            email,
            phone,
            password,
            role_id
        }, {
            onSuccess: () => {
                showCreateModal = false;
                resetForm();
            }
        });
    }

    function submitUpdate() {
        router.patch(`/admin/users/${selectedUser.id}`, {
            username,
            name,
            email,
            phone,
            role_id,
            ...(password ? { password } : {})
        }, {
            onSuccess: () => {
                showEditModal = false;
                selectedUser = null;
            }
        });
    }

    function toggleStatus(id: number) {
        if (confirm('Are you sure you want to toggle this user status?')) {
            router.post(`/admin/users/${id}/toggle`);
        }
    }

    function deleteUser(id: number) {
        if (confirm('Are you sure you want to delete this user?')) {
            router.delete(`/admin/users/${id}`);
        }
    }
</script>

<AppHead title="User Management" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">User Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Add, edit, ban, or delete accounts across the platform.</p>
        </div>
        <button
            onclick={openCreate}
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
        >
            Add New User
        </button>
    </div>

    <!-- Alert status -->
    {#if flash?.success}
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-550/20">
            {flash.success}
        </div>
    {/if}
    {#if flash?.error}
        <div class="p-4 rounded-xl bg-red-50 text-red-700 text-sm border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-550/20">
            {flash.error}
        </div>
    {/if}

    <!-- Table -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/50 text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">
                        <th class="p-4">User</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Last Login</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm text-slate-700 dark:text-slate-350">
                    {#each users as user}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-all duration-150">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white">{user.name}</div>
                                <div class="text-xs text-slate-400">@{user.username} • {user.email}</div>
                            </td>
                            <td class="p-4">{user.phone}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide
                                    {user.role === 'super-admin' ? 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400' : ''}
                                    {user.role === 'admin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : ''}
                                    {user.role === 'organizer' ? 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' : ''}
                                    {user.role === 'player' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400' : ''}
                                ">
                                    {user.role}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {user.status ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400'}
                                ">
                                    {user.status ? 'Active' : 'Banned'}
                                </span>
                            </td>
                            <td class="p-4 text-xs text-slate-450 dark:text-slate-450">{user.last_login || 'Never'}</td>
                            <td class="p-4 text-right space-x-2">
                                <button
                                    onclick={() => toggleStatus(user.id)}
                                    class="text-xs font-semibold px-2 py-1 rounded border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors"
                                >
                                    {user.status ? 'Ban' : 'Unban'}
                                </button>
                                <button
                                    onclick={() => openEdit(user)}
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                                >
                                    Edit
                                </button>
                                <button
                                    onclick={() => deleteUser(user.id)}
                                    class="text-xs font-semibold text-rose-600 dark:text-rose-450 hover:underline"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals -->
    {#if showCreateModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create New User</h3>
                <form onsubmit={submitCreate} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="username">Username</label>
                        <input bind:value={username} type="text" id="username" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="name">Full Name</label>
                        <input bind:value={name} type="text" id="name" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="email">Email</label>
                        <input bind:value={email} type="email" id="email" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="phone">Phone</label>
                        <input bind:value={phone} type="text" id="phone" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="password">Password</label>
                        <input bind:value={password} type="password" id="password" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="role_id">Role</label>
                        <select bind:value={role_id} id="role_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each roles as role}
                                <option value={role.id}>{role.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showCreateModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    {#if showEditModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit User</h3>
                <form onsubmit={submitUpdate} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="username">Username</label>
                        <input bind:value={username} type="text" id="username" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="name">Full Name</label>
                        <input bind:value={name} type="text" id="name" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="email">Email</label>
                        <input bind:value={email} type="email" id="email" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="phone">Phone</label>
                        <input bind:value={phone} type="text" id="phone" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="password">Password <span class="text-xs text-slate-400 normal-case">(leave blank to keep current)</span></label>
                        <input bind:value={password} type="password" id="password" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="role_id">Role</label>
                        <select bind:value={role_id} id="role_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each roles as role}
                                <option value={role.id}>{role.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showEditModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Update</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
