<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Squads', href: '/player/squads' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let {
        managedSquads = [],
        memberSquads = [],
        myPlayers = [],
        incomingRequests = [],
        incomingApplications = [],
        allSquads = [],
        myTeams = [],
        games = [],
        flash = {}
    }: {
        managedSquads: any[];
        memberSquads: any[];
        myPlayers: any[];
        incomingRequests: any[];
        incomingApplications: any[];
        allSquads: any[];
        myTeams: any[];
        games: any[];
        flash: any;
    } = $props();

    let showCreateModal = $state(false);
    let showApplyModal = $state(false);
    let showInviteModal = $state(false);

    // Create fields
    let team_id = $state('');
    let game_id = $state('');
    let name = $state('');
    let description = $state('');

    // Apply fields
    let applyPlayerId = $state('');
    let applySquadId = $state('');
    let applyNotes = $state('');

    // Invite fields
    let inviteSquadId = $state('');
    let invitePlayerId = $state('');
    let inviteNotes = $state('');

    function openCreate() {
        team_id = myTeams[0]?.id || '';
        game_id = games[0]?.id || '';
        name = '';
        description = '';
        showCreateModal = true;
    }

    function openApply() {
        applyPlayerId = myPlayers[0]?.id || '';
        applySquadId = allSquads[0]?.id || '';
        applyNotes = '';
        showApplyModal = true;
    }

    function openInvite(squadId: number) {
        inviteSquadId = squadId.toString();
        invitePlayerId = '';
        inviteNotes = '';
        showInviteModal = true;
    }

    function submitCreate() {
        router.post('/player/squads', {
            team_id,
            game_id,
            name,
            description
        }, {
            onSuccess: () => showCreateModal = false
        });
    }

    function submitApply() {
        router.post('/player/squads/requests', {
            squad_id: applySquadId,
            player_id: applyPlayerId,
            type: 'apply',
            notes: applyNotes
        }, {
            onSuccess: () => showApplyModal = false
        });
    }

    function submitInvite() {
        router.post('/player/squads/requests', {
            squad_id: inviteSquadId,
            player_id: invitePlayerId,
            type: 'invite',
            notes: inviteNotes
        }, {
            onSuccess: () => showInviteModal = false
        });
    }

    function handleRequest(id: number, action: 'approve' | 'reject' | 'cancel') {
        router.post(`/player/squads/requests/${id}/handle`, { action });
    }

    function releasePlayer(squadId: number, playerId: number) {
        if (confirm('Are you sure you want to release this player from the roster?')) {
            router.post(`/player/squads/${squadId}/release/${playerId}`);
        }
    }

    function disbandSquad(id: number) {
        if (confirm('Are you sure you want to disband this squad division? This cannot be undone.')) {
            router.delete(`/player/squads/${id}`);
        }
    }
</script>

<AppHead title="My Squads" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Squad Divisions</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage squad rosters, applications, invites, and roster memberships.</p>
        </div>
        <div class="space-x-3">
            <button
                onclick={openApply}
                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900 transition-colors"
                disabled={myPlayers.length === 0}
            >
                Apply to Squad
            </button>
            <button
                onclick={openCreate}
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
                disabled={myTeams.length === 0}
            >
                Create Squad Division
            </button>
        </div>
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

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left 2 Cols: Squads List -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Managed Squads -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Squads I Manage</h2>
                <div class="space-y-6">
                    {#each managedSquads as squad}
                        <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-900 bg-slate-50/30 dark:bg-slate-900/10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-slate-950 dark:text-white text-md">{squad.name}</h3>
                                    <p class="text-xs text-slate-400">Team: {squad.team?.name} • Game: {squad.game?.name}</p>
                                </div>
                                <div class="space-x-2">
                                    <button onclick={() => openInvite(squad.id)} class="text-xs font-semibold bg-indigo-50 text-indigo-600 dark:bg-indigo-550/10 dark:text-indigo-400 px-2.5 py-1 rounded-lg">Invite Player</button>
                                    <button onclick={() => disbandSquad(squad.id)} class="text-xs font-semibold text-rose-600 dark:text-rose-455 hover:underline">Disband</button>
                                </div>
                            </div>

                            <!-- Roster Members -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400 mb-2">Active Roster</h4>
                                <div class="divide-y divide-slate-100 dark:divide-slate-900">
                                    {#each squad.players || [] as player}
                                        <div class="py-2 flex justify-between items-center text-sm">
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-white">{player.nickname}</span>
                                                <span class="text-xs text-slate-400 ml-2">({player.name} • {player.game_role?.name || 'No Role'})</span>
                                            </div>
                                            <button onclick={() => releasePlayer(squad.id, player.id)} class="text-xs text-rose-500 hover:text-rose-650">Release</button>
                                        </div>
                                    {:else}
                                        <p class="text-xs text-slate-400 italic py-2">No active players on the roster.</p>
                                    {/each}
                                </div>
                            </div>
                        </div>
                    {:else}
                        <p class="text-sm text-slate-400 italic">No managed squads. Create one using the button above.</p>
                    {/each}
                </div>
            </div>

            <!-- Member Squads -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Squads I Play In</h2>
                <div class="space-y-4">
                    {#each memberSquads as squad}
                        <div class="rounded-xl border border-slate-100 p-4 dark:border-slate-900 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">{squad.name}</h3>
                                <p class="text-xs text-slate-400">Team: {squad.team?.name} • Game: {squad.game?.name}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Active Member</span>
                        </div>
                    {:else}
                        <p class="text-sm text-slate-400 italic">You are not a member of any external squads.</p>
                    {/each}
                </div>
            </div>
        </div>

        <!-- Right Col: Requests & Applications -->
        <div class="space-y-6">
            <!-- Incoming Invites -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-md font-bold text-slate-900 dark:text-white mb-4">Roster Invitations ({incomingRequests.length})</h3>
                <div class="space-y-4">
                    {#each incomingRequests as req}
                        <div class="p-3 border border-slate-100 rounded-xl dark:border-slate-900 text-sm">
                            <p class="font-semibold text-slate-900 dark:text-white">Invite to {req.squad?.name}</p>
                            <p class="text-xs text-slate-400">For profile: {req.player?.nickname} ({req.player?.name})</p>
                            {#if req.notes}
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">"{req.notes}"</p>
                            {/if}
                            <div class="mt-3 flex justify-end gap-2">
                                <button onclick={() => handleRequest(req.id, 'reject')} class="text-xs font-semibold border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded hover:bg-slate-50">Reject</button>
                                <button onclick={() => handleRequest(req.id, 'approve')} class="text-xs font-semibold bg-indigo-600 text-white px-2.5 py-1 rounded hover:bg-indigo-500">Accept</button>
                            </div>
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 italic">No pending invitations.</p>
                    {/each}
                </div>
            </div>

            <!-- Incoming Applications -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-md font-bold text-slate-900 dark:text-white mb-4"> Roster Applications ({incomingApplications.length})</h3>
                <div class="space-y-4">
                    {#each incomingApplications as app}
                        <div class="p-3 border border-slate-100 rounded-xl dark:border-slate-900 text-sm">
                            <p class="font-semibold text-slate-900 dark:text-white">@{app.player?.nickname} wants to join {app.squad?.name}</p>
                            <p class="text-xs text-slate-400">Profile Name: {app.player?.name} • Position: {app.player?.game_role?.name}</p>
                            {#if app.notes}
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">"{app.notes}"</p>
                            {/if}
                            <div class="mt-3 flex justify-end gap-2">
                                <button onclick={() => handleRequest(app.id, 'reject')} class="text-xs font-semibold border border-slate-200 dark:border-slate-800 px-2.5 py-1 rounded hover:bg-slate-50">Reject</button>
                                <button onclick={() => handleRequest(app.id, 'approve')} class="text-xs font-semibold bg-indigo-600 text-white px-2.5 py-1 rounded hover:bg-indigo-500">Approve</button>
                            </div>
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 italic">No pending applications.</p>
                    {/each}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    {#if showCreateModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Create Squad Division</h3>
                <form onsubmit={submitCreate} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="sqTeam">Parent Team Organization</label>
                        <select bind:value={team_id} id="sqTeam" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each myTeams as t}
                                <option value={t.id}>{t.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="sqGame">Game Division</label>
                        <select bind:value={game_id} id="sqGame" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each games as g}
                                <option value={g.id}>{g.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="sqName">Squad Division Name</label>
                        <input bind:value={name} type="text" id="sqName" required placeholder="e.g. RRQ Hoshi, RRQ Akira" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="sqDesc">Description</label>
                        <textarea bind:value={description} id="sqDesc" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showCreateModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    <!-- Apply Modal -->
    {#if showApplyModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Apply to Join a Squad</h3>
                <form onsubmit={submitApply} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="appPlayer">My Player Profile</label>
                        <select bind:value={applyPlayerId} id="appPlayer" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each myPlayers as p}
                                <option value={p.id}>{p.nickname} (Game: {p.game?.name})</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="appSquad">Target Squad Division</label>
                        <select bind:value={applySquadId} id="appSquad" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each allSquads as s}
                                <option value={s.id}>{s.name} (Game: {s.game?.name})</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="appNotes">Application Note</label>
                        <textarea bind:value={applyNotes} id="appNotes" rows="3" placeholder="Explain why you want to join..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showApplyModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    <!-- Invite Modal -->
    {#if showInviteModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Invite Player to Squad</h3>
                <form onsubmit={submitInvite} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="invPlayer">Player ID</label>
                        <input bind:value={invitePlayerId} type="number" id="invPlayer" required placeholder="Enter player ID..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="invNotes">Invitation Message</label>
                        <textarea bind:value={inviteNotes} id="invNotes" rows="3" placeholder="Write a welcoming invitation..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showInviteModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Send Invitation</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
