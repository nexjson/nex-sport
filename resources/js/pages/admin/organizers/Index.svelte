<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Organizers Management', href: '/admin/organizers' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { organizers = [], availableUsers = [], flash = {} }: { organizers: any[]; availableUsers: any[]; flash: any } = $props();

    let showCreateModal = $state(false);
    let showEditModal = $state(false);
    let selectedOrg = $state<any>(null);

    // Form fields
    let name = $state('');
    let description = $state('');
    let user_id = $state('');
    let status = $state(true);

    function openCreate() {
        name = '';
        description = '';
        user_id = availableUsers[0]?.id || '';
        status = true;
        showCreateModal = true;
    }

    function openEdit(org: any) {
        selectedOrg = org;
        name = org.name;
        description = org.description || '';
        status = org.status;
        showEditModal = true;
    }

    function submitCreate() {
        router.post('/admin/organizers', {
            name,
            description,
            user_id,
            status
        }, {
            onSuccess: () => showCreateModal = false
        });
    }

    function submitUpdate() {
        router.patch(`/admin/organizers/${selectedOrg.id}`, {
            name,
            description,
            status
        }, {
            onSuccess: () => showEditModal = false
        });
    }

    function deleteOrg(id: number) {
        if (confirm('Are you sure you want to delete this organizer profile?')) {
            router.delete(`/admin/organizers/${id}`);
        }
    }
</script>

<AppHead title="Organizers Management" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Organizers Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Assign users with organizer roles to profiles and track their status.</p>
        </div>
        <button
            onclick={openCreate}
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
            disabled={availableUsers.length === 0}
            title={availableUsers.length === 0 ? 'All organizer users already have profiles' : ''}
        >
            Add Organizer Profile
        </button>
    </div>

    <!-- Alerts -->
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

    <!-- Organizers List -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/50 text-xs font-semibold uppercase tracking-wider text-slate-450 dark:text-slate-400">
                        <th class="p-4">Organizer Name</th>
                        <th class="p-4">Assigned User</th>
                        <th class="p-4">Description</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm text-slate-700 dark:text-slate-350">
                    {#each organizers as org}
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-all duration-150">
                            <td class="p-4 font-bold text-slate-900 dark:text-white">{org.name}</td>
                            <td class="p-4">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{org.user?.name}</div>
                                <div class="text-xs text-slate-400">@{org.user?.username} • {org.user?.email}</div>
                            </td>
                            <td class="p-4 max-w-xs truncate text-slate-500 dark:text-slate-400">{org.description || '-'}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {org.status ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-750 dark:bg-slate-850 dark:text-slate-400'}
                                ">
                                    {org.status ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button
                                    onclick={() => openEdit(org)}
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                                >
                                    Edit
                                </button>
                                <button
                                    onclick={() => deleteOrg(org.id)}
                                    class="text-xs font-semibold text-rose-600 dark:text-rose-455 hover:underline"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-400 italic">No organizer profiles configured yet.</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    {#if showCreateModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create Organizer Profile</h3>
                <form onsubmit={submitCreate} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="orgName">Organizer Name</label>
                        <input bind:value={name} type="text" id="orgName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="orgUser">Assign to User</label>
                        <select bind:value={user_id} id="orgUser" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each availableUsers as u}
                                <option value={u.id}>{u.name} (@{u.username})</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="orgDesc">Description</label>
                        <textarea bind:value={description} id="orgDesc" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="orgStatus">Status</label>
                        <select bind:value={status} id="orgStatus" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            <option value={true}>Active</option>
                            <option value={false}>Inactive</option>
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

    <!-- Edit Modal -->
    {#if showEditModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Organizer Profile</h3>
                <form onsubmit={submitUpdate} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eOrgName">Organizer Name</label>
                        <input bind:value={name} type="text" id="eOrgName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eOrgDesc">Description</label>
                        <textarea bind:value={description} id="eOrgDesc" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eOrgStatus">Status</label>
                        <select bind:value={status} id="eOrgStatus" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            <option value={true}>Active</option>
                            <option value={false}>Inactive</option>
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
