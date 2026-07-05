<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'My Tournaments', href: '/organizer/events' },
            { title: 'Matches & Bracket', href: '/organizer/matches' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { eventGame = {}, matches = [], standings = [], flash = {} }: { eventGame: any; matches: any[]; standings: any[]; flash: any } = $props();

    let showScoreModal = $state(false);
    let selectedMatch = $state<any>(null);

    // Score & Schedule fields
    let homeScore = $state(0);
    let awayScore = $state(0);
    let scheduled_at = $state('');

    // Group matches by round for visual presentation
    const maxRound = Math.max(...matches.map(m => m.round), 1);
    const roundsList = Array.from({ length: maxRound }, (_, i) => i + 1);

    function openScoreModal(match: any) {
        selectedMatch = match;
        homeScore = match.score_home || 0;
        awayScore = match.score_away || 0;
        scheduled_at = match.scheduled_at ? match.scheduled_at.substring(0, 16) : '';
        showScoreModal = true;
    }

    function submitScore() {
        if (scheduled_at) {
            router.post(`/organizer/matches/${selectedMatch.id}/schedule`, {
                scheduled_at: scheduled_at
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    if (homeScore !== selectedMatch.score_home || awayScore !== selectedMatch.score_away) {
                        saveScore();
                    } else {
                        showScoreModal = false;
                    }
                }
            });
        } else {
            saveScore();
        }
    }

    function saveScore() {
        router.post(`/organizer/matches/${selectedMatch.id}/score`, {
            squad_home_score: homeScore,
            squad_away_score: awayScore
        }, {
            onSuccess: () => showScoreModal = false
        });
    }

    function updateStatus(status: string) {
        router.post(`/organizer/matches/${selectedMatch.id}/status`, { status }, {
            onSuccess: () => showScoreModal = false
        });
    }

    function generateBracket() {
        if (confirm('Are you sure you want to generate bracket matches for this division?')) {
            router.post(`/organizer/matches/${eventGame.id}/generate`);
        }
    }
</script>

<AppHead title="Tournament Bracket" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{eventGame.event?.name} — Bracket</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Division: {eventGame.game?.name} ({eventGame.max_squads} Squads)</p>
        </div>
        {#if matches.length === 0}
            <button
                onclick={generateBracket}
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
            >
                Generate Bracket Matches
            </button>
        {/if}
    </div>

    <!-- Alerts -->
    {#if flash?.success}
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-550/20">
            {flash.success}
        </div>
    {/if}
    {#if flash?.error}
        <div class="p-4 rounded-xl bg-rose-50 text-rose-700 text-sm border border-rose-200 dark:bg-rose-500/10 dark:text-rose-450 dark:border-rose-550/20">
            {flash.error}
        </div>
    {/if}

    {#if matches.length > 0}
        {#if standings && standings.length > 0}
            <!-- Standings Table (for Round Robin) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 mb-6">
                <h3 class="text-md font-bold text-slate-900 dark:text-white mb-4">Round Robin Standings</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-900 text-xs font-bold uppercase tracking-wider text-slate-400">
                                <th class="py-3 px-4">Rank</th>
                                <th class="py-3 px-4">Squad</th>
                                <th class="py-3 px-4 text-center">Wins</th>
                                <th class="py-3 px-4 text-center">Losses</th>
                                <th class="py-3 px-4 text-center">Draws</th>
                                <th class="py-3 px-4 text-center font-bold">Points</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-900">
                            {#each standings as std, index}
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10">
                                    <td class="py-3 px-4 font-bold">{index + 1}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                        {std.squad?.name || 'Unknown Squad'}
                                    </td>
                                    <td class="py-3 px-4 text-center">{std.wins}</td>
                                    <td class="py-3 px-4 text-center">{std.losses}</td>
                                    <td class="py-3 px-4 text-center">{std.draws}</td>
                                    <td class="py-3 px-4 text-center font-bold text-indigo-650 dark:text-indigo-400">{std.points}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}

        <!-- Bracket Visual Grid -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Tournament Seeding</h3>
            <div class="flex gap-8 overflow-x-auto pb-4">
                {#each roundsList as round}
                    <div class="min-w-[220px] flex flex-col justify-around gap-6">
                        <div class="text-center font-bold text-xs uppercase tracking-wider text-slate-400 mb-2 border-b pb-2">Round {round}</div>
                        {#each matches.filter(m => m.round === round) as match}
                            <div class="rounded-xl border border-slate-150 p-4 bg-white dark:border-slate-850 dark:bg-slate-900/30 flex flex-col gap-2 relative">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold {match.winner_id === match.squad_home_id ? 'text-indigo-600 dark:text-indigo-400 font-bold' : ''}">
                                        {match.squad_home?.team?.name || 'Waiting...'}
                                    </span>
                                    <span>{match.score_home !== null ? match.score_home : '-'}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold {match.winner_id === match.squad_away_id ? 'text-indigo-600 dark:text-indigo-400 font-bold' : ''}">
                                        {match.squad_away?.team?.name || 'Waiting...'}
                                    </span>
                                    <span>{match.score_away !== null ? match.score_away : '-'}</span>
                                </div>

                                {#if match.scheduled_at}
                                    <div class="text-[10px] text-slate-400 italic mt-1 border-t pt-1">
                                        Schedule: {match.scheduled_at.substring(0, 16).replace('T', ' ')}
                                    </div>
                                {/if}

                                <div class="absolute top-2 right-2 text-[9px] uppercase font-bold tracking-wider rounded px-1.5 py-0.5
                                    {match.status === 'live' ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/20' : 'bg-slate-50 text-slate-500 dark:bg-slate-800'}
                                ">
                                    {match.status}
                                </div>

                                {#if match.squad_home_id && match.squad_away_id}
                                    <button
                                        onclick={() => openScoreModal(match)}
                                        class="mt-2 w-full text-center text-xs font-bold text-indigo-650 dark:text-indigo-400 hover:underline border-t pt-2"
                                    >
                                        {match.status === 'completed' ? 'Edit Match details' : 'Manage Match'}
                                    </button>
                                {/if}
                            </div>
                        {/each}
                    </div>
                {/each}
            </div>
        </div>
    {:else}
        <div class="rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center dark:border-slate-850">
            <p class="text-sm text-slate-400 italic">No bracket matches have been generated. Click the button to start the tournament.</p>
        </div>
    {/if}
</div>

<!-- Score & Schedule Management Modal -->
{#if showScoreModal}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Manage Match</h3>
            <form onsubmit={submitScore} class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{selectedMatch?.squad_home?.team?.name}</label>
                        <input bind:value={homeScore} type="number" required min="0" class="w-full text-center rounded-xl border border-slate-200 px-4 py-2 text-md focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                    <span class="text-slate-400 font-bold">vs</span>
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{selectedMatch?.squad_away?.team?.name}</label>
                        <input bind:value={awayScore} type="number" required min="0" class="w-full text-center rounded-xl border border-slate-200 px-4 py-2 text-md focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="schedTime">Scheduled At</label>
                    <input bind:value={scheduled_at} type="datetime-local" id="schedTime" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-900">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Update Status</label>
                    <div class="flex gap-2">
                        {#each ['scheduled', 'live', 'cancelled'] as st}
                            {#if selectedMatch?.status !== st}
                                <button type="button" onclick={() => updateStatus(st)} class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 py-1.5 px-3 rounded-lg text-xs font-semibold capitalize flex-1 text-center transition-colors">
                                    Set {st}
                                </button>
                            {/if}
                        {/each}
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-900">
                    <button type="button" onclick={() => showScoreModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save Result</button>
                </div>
            </form>
        </div>
    </div>
{/if}
