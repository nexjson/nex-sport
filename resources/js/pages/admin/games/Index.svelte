<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Games Management', href: '/admin/games' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { games = [], flash = {} }: { games: any[]; flash: any } = $props();

    let showGameModal = $state(false);
    let showRoleModal = $state(false);
    let isEditingGame = $state(false);
    let selectedGame = $state<any>(null);

    // Game form fields
    let gameName = $state('');
    let gameCategory = $state('esports');
    let gameStatus = $state(true);

    // Role form fields
    let roleName = $state('');
    let roleStatus = $state(true);

    function openCreateGame() {
        gameName = '';
        gameCategory = 'esports';
        gameStatus = true;
        isEditingGame = false;
        showGameModal = true;
    }

    function openEditGame(game: any) {
        selectedGame = game;
        gameName = game.name;
        gameCategory = game.category;
        gameStatus = game.status;
        isEditingGame = true;
        showGameModal = true;
    }

    function openAddRole(game: any) {
        selectedGame = game;
        roleName = '';
        roleStatus = true;
        showRoleModal = true;
    }

    function submitGame() {
        if (isEditingGame) {
            router.patch(`/admin/games/${selectedGame.id}`, {
                name: gameName,
                category: gameCategory,
                status: gameStatus
            }, {
                onSuccess: () => showGameModal = false
            });
        } else {
            router.post('/admin/games', {
                name: gameName,
                category: gameCategory,
                status: gameStatus
            }, {
                onSuccess: () => showGameModal = false
            });
        }
    }

    function submitRole() {
        router.post(`/admin/games/${selectedGame.id}/roles`, {
            name: roleName,
            status: roleStatus
        }, {
            onSuccess: () => showRoleModal = false
        });
    }

    function deleteGame(id: number) {
        if (confirm('Are you sure you want to delete this game?')) {
            router.delete(`/admin/games/${id}`);
        }
    }

    function deleteRole(roleId: number) {
        if (confirm('Are you sure you want to delete this game role?')) {
            router.delete(`/admin/games/roles/${roleId}`);
        }
    }
</script>

<AppHead title="Games Management" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Games & Ranks Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage sport/esport branches and defining specialized player positions.</p>
        </div>
        <button
            onclick={openCreateGame}
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
        >
            Add New Game
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

    <!-- Games Grid -->
    <div class="grid gap-6 md:grid-cols-2">
        {#each games as game}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{game.name}</h3>
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-550/10 dark:text-indigo-400 uppercase">
                                {game.category}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                {game.status ? 'bg-emerald-50 text-emerald-750 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'}
                            ">
                                {game.status ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>

                    <!-- Roles List -->
                    <div class="mt-4 border-t border-slate-100 pt-4 dark:border-slate-900">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Game Roles / Roster Positions</h4>
                            <button onclick={() => openAddRole(game)} class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">+ Add Role</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            {#each game.roles as role}
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50/50 px-2.5 py-1 text-xs text-slate-650 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                                    {role.name}
                                    <button onclick={() => deleteRole(role.id)} class="text-slate-400 hover:text-rose-500 font-bold ml-1" title="Delete role">×</button>
                                </span>
                            {:else}
                                <span class="text-xs text-slate-400 italic">No roles configured yet.</span>
                            {/each}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-900">
                    <button onclick={() => openEditGame(game)} class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:underline">Edit Info</button>
                    <button onclick={() => deleteGame(game.id)} class="text-xs font-semibold text-rose-600 dark:text-rose-450 hover:underline">Delete Game</button>
                </div>
            </div>
        {/each}
    </div>

    <!-- Game Form Modal -->
    {#if showGameModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {isEditingGame ? 'Edit Game' : 'Add New Game'}
                </h3>
                <form onsubmit={submitGame} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="gName">Game Name</label>
                        <input bind:value={gameName} type="text" id="gName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="gCat">Category</label>
                        <select bind:value={gameCategory} id="gCat" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            <option value="esports">Esports</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="gStatus">Status</label>
                        <select bind:value={gameStatus} id="gStatus" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            <option value={true}>Active</option>
                            <option value={false}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showGameModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    <!-- Role Form Modal -->
    {#if showRoleModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    Add Role for {selectedGame?.name}
                </h3>
                <form onsubmit={submitRole} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="rName">Role / Position Name</label>
                        <input bind:value={roleName} type="text" id="rName" required placeholder="e.g. Tank, Goalkeeper, Striker" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="rStatus">Status</label>
                        <select bind:value={roleStatus} id="rStatus" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            <option value={true}>Active</option>
                            <option value={false}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showRoleModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
