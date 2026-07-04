<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Teams', href: '/player/teams' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { teams = [], flash = {} }: { teams: any[]; flash: any } = $props();

    let showModal = $state(false);
    let isEditing = $state(false);
    let selectedTeam = $state<any>(null);

    // Form fields
    let name = $state('');
    let short_name = $state('');
    let logo = $state('');
    let description = $state('');

    function openCreate() {
        name = '';
        short_name = '';
        logo = '';
        description = '';
        isEditing = false;
        showModal = true;
    }

    function openEdit(team: any) {
        selectedTeam = team;
        name = team.name;
        short_name = team.short_name || '';
        logo = team.logo || '';
        description = team.description || '';
        isEditing = true;
        showModal = true;
    }

    function submit() {
        if (isEditing) {
            router.patch(`/player/teams/${selectedTeam.id}`, {
                name,
                short_name,
                logo,
                description
            }, {
                onSuccess: () => showModal = false
            });
        } else {
            router.post('/player/teams', {
                name,
                short_name,
                logo,
                description
            }, {
                onSuccess: () => showModal = false
            });
        }
    }

    function deleteTeam(id: number) {
        if (confirm('Are you sure you want to delete this team?')) {
            router.delete(`/player/teams/${id}`);
        }
    }
</script>

<AppHead title="My Teams" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">My Organizations / Teams</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage your main gaming organizations and clubs.</p>
        </div>
        <button
            onclick={openCreate}
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
        >
            Create New Team
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

    <!-- Team Cards Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {#each teams as team}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 overflow-hidden rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center font-bold">
                            {team.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white">{team.name}</h3>
                            <p class="text-xs text-slate-400">{team.squads_count} active squads</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3">{team.description || 'No description provided.'}</p>
                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-900">
                    <button onclick={() => openEdit(team)} class="text-xs font-semibold text-indigo-650 dark:text-indigo-400 hover:underline">Edit</button>
                    <button onclick={() => deleteTeam(team.id)} class="text-xs font-semibold text-rose-600 dark:text-rose-455 hover:underline">Delete</button>
                </div>
            </div>
        {:else}
            <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center dark:border-slate-800">
                <p class="text-sm text-slate-400 italic mb-4">You do not own any team organizations yet.</p>
                <button onclick={openCreate} class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">Create First Team</button>
            </div>
        {/each}
    </div>

    <!-- Modal Form -->
    {#if showModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {isEditing ? 'Edit Team Organization' : 'Create Team Organization'}
                </h3>
                <form onsubmit={submit} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="tName">Team Name</label>
                        <input bind:value={name} type="text" id="tName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="tShortName">Short Name (Abbreviation)</label>
                        <input bind:value={short_name} type="text" id="tShortName" required placeholder="e.g. RRQ, EVOS, ONIC" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="tDesc">Description</label>
                        <textarea bind:value={description} id="tDesc" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
