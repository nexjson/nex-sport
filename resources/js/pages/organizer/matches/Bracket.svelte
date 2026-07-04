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

    // Score fields
    let homeScore = $state(0);
    let awayScore = $state(0);

    // Group matches by round for visual presentation
    const maxRound = Math.max(...matches.map(m => m.round), 1);
    const roundsList = Array.from({ length: maxRound }, (_, i) => i + 1);

    function openScoreModal(match: any) {
        selectedMatch = match;
        homeScore = match.score_home || 0;
        awayScore = match.score_away || 0;
        showScoreModal = true;
    }

    function submitScore() {
        router.post(`/organizer/matches/${selectedMatch.id}/score`, {
            squad_home_score: homeScore,
            squad_away_score: awayScore
        }, {
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
        <!-- Bracket Visual Grid -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Tournament Seeding</h3>
            <div class="flex gap-8 overflow-x-auto pb-4">
                {#each roundsList as round}
                    <div class="min-w-[220px] flex flex-col justify-around gap-6">
                        <div class="text-center font-bold text-xs uppercase tracking-wider text-slate-400 mb-2 border-b pb-2">Round {round}</div>
                        {#each matches.filter(m => m.round === round) as match}
                            <div class="rounded-xl border border-slate-150 p-4 bg-slate-50/50 dark:border-slate-850 dark:bg-slate-900/30 flex flex-col gap-2 relative">
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

                                {#if match.status !== 'completed' && match.squad_home_id && match.squad_away_id}
                                    <button
                                        onclick={() => openScoreModal(match)}
                                        class="mt-2 w-full text-center text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline border-t pt-2"
                                    >
                                        Record Result
                                    </button>
                                {:else if match.status === 'completed'}
                                    <div class="mt-2 border-t pt-2 text-[10px] text-emerald-500 font-bold text-center">Completed</div>
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

<!-- Score Input Modal -->
{#if showScoreModal}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Record Match Score</h3>
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

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick={() => showScoreModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save Result</button>
                </div>
            </form>
        </div>
    </div>
{/if}
