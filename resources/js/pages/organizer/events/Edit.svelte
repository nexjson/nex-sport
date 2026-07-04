<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'My Tournaments', href: '/organizer/events' },
            { title: 'Tournament Setup', href: '/organizer/events/edit' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { event = {}, games = [], flash = {} }: { event: any; games: any[]; flash: any } = $props();

    // Event details
    let name = $state(event.name || '');
    let description = $state(event.description || '');
    let start_date = $state(event.start_date || '');
    let end_date = $state(event.end_date || '');

    // Modals toggle
    let showGameModal = $state(false);
    let showSponsorModal = $state(false);

    // Game division form
    let game_id = $state(games[0]?.id || '');
    let max_squads = $state(8);
    let ticket_price = $state(0);
    let admin_ticket_fee = $state(0);

    // Sponsor form
    let sponsorName = $state('');
    let sponsorLogo = $state('');
    let sponsorWebsite = $state('');

    function updateEvent() {
        router.patch(`/organizer/events/${event.id}`, {
            name,
            description,
            start_date,
            end_date
        });
    }

    function addGame() {
        router.post(`/organizer/events/${event.id}/games`, {
            game_id,
            max_squads,
            ticket_price,
            admin_ticket_fee
        }, {
            onSuccess: () => showGameModal = false
        });
    }

    function removeGame(eventGamesId: number) {
        if (confirm('Are you sure you want to remove this game division?')) {
            router.delete(`/organizer/events/${event.id}/games/${eventGamesId}`);
        }
    }

    function addSponsor() {
        router.post(`/organizer/events/${event.id}/sponsors`, {
            name: sponsorName,
            logo: sponsorLogo,
            website: sponsorWebsite
        }, {
            onSuccess: () => showSponsorModal = false
        });
    }

    function payDeposit() {
        if (confirm('Verify publishing and pay deposit fee?')) {
            router.post(`/organizer/events/${event.id}/pay`);
        }
    }

    function approveRegistration(regId: number) {
        if (confirm('Approve this squad registration?')) {
            router.post(`/player/registrations/${regId}/process`, { action: 'approved' });
        }
    }

    function rejectRegistration(regId: number) {
        if (confirm('Reject this squad registration?')) {
            router.post(`/player/registrations/${regId}/process`, { action: 'rejected' });
        }
    }
</script>

<AppHead title="Tournament Setup" />

<div class="space-y-6 p-6 max-w-6xl mx-auto">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tournament Setup & Management</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Configure divisions, publish deposit fees, and approve participating squads.</p>
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
        <!-- Left 2 Cols: Configuration details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Details -->
            <form onsubmit={updateEvent} class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 space-y-4">
                <h3 class="text-md font-bold text-slate-900 dark:text-white">1. Basic Details</h3>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eName">Tournament Name</label>
                    <input bind:value={name} type="text" id="eName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eDesc">Description</label>
                    <textarea bind:value={description} id="eDesc" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="sDate">Start Date</label>
                        <input bind:value={start_date} type="date" id="sDate" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eDate">End Date</label>
                        <input bind:value={end_date} type="date" id="eDate" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-700">Update Info</button>
                </div>
            </form>

            <!-- Game Divisions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-md font-bold text-slate-900 dark:text-white">2. Game Divisions</h3>
                    {#if event.status === 'draft'}
                        <button onclick={() => showGameModal = true} class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">+ Add Division</button>
                    {/if}
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-900">
                    {#each event.event_games || [] as div}
                        <div class="py-3 flex justify-between items-center text-sm">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white">{div.game?.name}</span>
                                <span class="text-xs text-slate-400 ml-2">({div.max_squads} Squads limit • Entry: Rp {div.ticket_price + div.admin_ticket_fee})</span>
                            </div>
                            {#if event.status === 'draft'}
                                <button onclick={() => removeGame(div.id)} class="text-xs text-rose-500 hover:text-rose-650">Remove</button>
                            {/if}
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 italic py-2">No game divisions added yet.</p>
                    {/each}
                </div>
            </div>

            <!-- Registrations Review -->
            {#if event.status !== 'draft'}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                    <h3 class="text-md font-bold text-slate-900 dark:text-white mb-4">3. Roster Registrations Queue</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-105 text-xs font-medium text-slate-400">
                                    <th class="py-2">Squad</th>
                                    <th class="py-2">Division</th>
                                    <th class="py-2">Payment Status</th>
                                    <th class="py-2">Review Status</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm">
                                {#each event.event_games || [] as eg}
                                    {#each eg.registrations || [] as reg}
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10">
                                            <td class="py-3">
                                                <span class="font-bold text-slate-900 dark:text-white">{reg.squad?.name}</span>
                                                <span class="text-xs text-slate-400 block">Team: {reg.squad?.team?.name}</span>
                                            </td>
                                            <td class="py-3">{eg.game?.name}</td>
                                            <td class="py-3">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                                    {reg.payment_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}
                                                ">
                                                    {reg.payment_status}
                                                </span>
                                            </td>
                                            <td class="py-3 capitalize">{reg.status}</td>
                                            <td class="py-3 text-right space-x-2">
                                                {#if reg.status === 'pending' && reg.payment_status === 'completed'}
                                                    <button onclick={() => approveRegistration(reg.id)} class="text-xs bg-emerald-600 text-white px-2 py-0.5 rounded">Approve</button>
                                                    <button onclick={() => rejectRegistration(reg.id)} class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded">Reject</button>
                                                {:else}
                                                    <span class="text-xs text-slate-400 italic">No Action Needed</span>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/each}
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            {/if}
        </div>

        <!-- Right Col: Sponsors & Deposit Publication -->
        <div class="space-y-6">
            <!-- Publishing Payment -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-md font-bold text-slate-900 dark:text-white mb-2">Publish Tournament</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">To publish your tournament and open registrations, please pay the publishing deposit (escrow prizepool + platform fees).</p>
                {#if event.status === 'draft'}
                    <button onclick={payDeposit} class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">Publish & Pay Deposit</button>
                {:else}
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/20 text-center">
                        <span class="text-sm font-bold text-emerald-750 dark:text-emerald-400">Tournament Published</span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Registrations are active.</p>
                    </div>
                {/if}
            </div>

            <!-- Sponsors Panel -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-md font-bold text-slate-900 dark:text-white">Sponsors</h3>
                    <button onclick={() => showSponsorModal = true} class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">+ Add Sponsor</button>
                </div>
                <div class="space-y-3">
                    {#each event.event_sponsors || [] as sp}
                        <div class="flex items-center gap-3 p-2 bg-slate-50/50 rounded-xl dark:bg-slate-900/30">
                            <div class="h-8 w-8 rounded-lg bg-indigo-500/10 flex items-center justify-center font-bold text-indigo-500">
                                {sp.name.substring(0, 1).toUpperCase()}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{sp.name}</p>
                                <p class="text-xs text-slate-400 truncate max-w-[150px]">{sp.website || '-'}</p>
                            </div>
                        </div>
                    {:else}
                        <p class="text-xs text-slate-400 italic">No sponsors added.</p>
                    {/each}
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    {#if showGameModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Game Division</h3>
                <form onsubmit={addGame} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="adGame">Select Game</label>
                        <select bind:value={game_id} id="adGame" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each games as g}
                                <option value={g.id}>{g.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="adQuota">Maximum Squads</label>
                        <input bind:value={max_squads} type="number" id="adQuota" required min="2" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="adPrice">Ticket Price (Rp)</label>
                        <input bind:value={ticket_price} type="number" id="adPrice" required min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="adFee">Platform Fee per Ticket (Rp)</label>
                        <input bind:value={admin_ticket_fee} type="number" id="adFee" required min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showGameModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Add</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}

    {#if showSponsorModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Sponsor</h3>
                <form onsubmit={addSponsor} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="spName">Sponsor Name</label>
                        <input bind:value={sponsorName} type="text" id="spName" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="spLogo">Logo URL</label>
                        <input bind:value={sponsorLogo} type="text" id="spLogo" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="spWeb">Website URL</label>
                        <input bind:value={sponsorWebsite} type="url" id="spWeb" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showSponsorModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save Sponsor</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
