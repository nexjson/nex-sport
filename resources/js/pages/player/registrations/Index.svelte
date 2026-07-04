<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Tournament Registrations', href: '/player/registrations' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { mySquads = [], registrations = [], events = [], flash = {} }: { mySquads: any[]; registrations: any[]; events: any[]; flash: any } = $props();

    let showRegisterModal = $state(false);
    let selectedEventGame = $state<any>(null);
    let selectedEventName = $state('');

    // Form fields
    let squad_id = $state('');

    function openRegister(eventGame: any, eventName: string) {
        selectedEventGame = eventGame;
        selectedEventName = eventName;
        // Filter mySquads to find ones that match the event game division
        const compatibleSquads = mySquads.filter(s => s.game_id === eventGame.game_id);
        squad_id = compatibleSquads[0]?.id || '';
        showRegisterModal = true;
    }

    function submitRegistration() {
        router.post('/player/registrations', {
            squad_id,
            event_games_id: selectedEventGame.id
        }, {
            onSuccess: () => showRegisterModal = false
        });
    }

    function payTicket(regId: number) {
        if (confirm('Pay ticket registration fee?')) {
            router.post(`/player/registrations/${regId}/pay`);
        }
    }

    function cancelRegistration(regId: number) {
        if (confirm('Cancel this registration? Refund will be processed.')) {
            router.post(`/player/registrations/${regId}/cancel`);
        }
    }
</script>

<AppHead title="Tournament Registrations" />

<div class="space-y-6 p-6 max-w-6xl mx-auto">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tournament Entries & Tickets</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Browse open tournaments, join divisions, and verify paid passes.</p>
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
        <!-- Left 2 Cols: Browse Open Tournaments -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Open Tournaments</h2>
                <div class="space-y-6">
                    {#each events as event}
                        <div class="p-4 border border-slate-100 rounded-xl dark:border-slate-900 bg-slate-50/20 dark:bg-slate-900/10 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-slate-950 dark:text-white text-md">{event.name}</h3>
                                    <p class="text-xs text-slate-400">By: {event.organizer?.name} • Dates: {new Date(event.start_date).toLocaleDateString()} - {new Date(event.end_date).toLocaleDateString()}</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{event.description}</p>

                            <!-- Divisions list inside tournament -->
                            <div class="border-t border-slate-100 pt-3 dark:border-slate-900 space-y-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Available Divisions</h4>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    {#each event.event_games || [] as eg}
                                        <div class="p-3 bg-white border border-slate-100 rounded-xl dark:bg-slate-950 dark:border-slate-900 flex justify-between items-center text-sm">
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white">{eg.game?.name}</p>
                                                <p class="text-xs text-slate-400">Entry Fee: {eg.ticket_price + eg.admin_ticket_fee > 0 ? `Rp ${eg.ticket_price + eg.admin_ticket_fee}` : 'Free'}</p>
                                            </div>
                                            <button
                                                onclick={() => openRegister(eg, event.name)}
                                                class="rounded-lg bg-indigo-650 px-2.5 py-1 text-xs font-semibold text-white hover:bg-indigo-500 transition-colors"
                                                disabled={mySquads.filter(s => s.game_id === eg.game_id).length === 0}
                                                title={mySquads.filter(s => s.game_id === eg.game_id).length === 0 ? 'You do not own a squad for this game' : ''}
                                            >
                                                Register
                                            </button>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        </div>
                    {:else}
                        <p class="text-sm text-slate-400 italic">No open tournaments available for registration right now.</p>
                    {/each}
                </div>
            </div>
        </div>

        <!-- Right Col: My Squad Registrations -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">My Registrations</h2>
            <div class="space-y-4">
                {#each registrations as reg}
                    <div class="p-3 border border-slate-100 rounded-xl dark:border-slate-900 text-sm space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{reg.event_game?.event?.name}</p>
                                <p class="text-xs text-slate-400">Squad: {reg.squad?.name} ({reg.event_game?.game?.name})</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold uppercase
                                {reg.status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ''}
                                {reg.status === 'pending' ? 'bg-amber-50 text-amber-700' : ''}
                                {reg.status === 'rejected' ? 'bg-rose-50 text-rose-700' : ''}
                                {reg.status === 'cancelled' ? 'bg-slate-100 text-slate-700' : ''}
                            ">
                                {reg.status}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-xs text-slate-450 dark:text-slate-400 border-t border-slate-50 pt-2 dark:border-slate-900">
                            <span>Payment: <strong class="uppercase text-slate-650 dark:text-slate-350">{reg.payment_status}</strong></span>
                            {#if reg.status === 'pending'}
                                <div class="space-x-1">
                                    {#if reg.payment_status === 'pending'}
                                        <button onclick={() => payTicket(reg.id)} class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded hover:bg-indigo-500">Pay</button>
                                    {/if}
                                    <button onclick={() => cancelRegistration(reg.id)} class="text-xs text-rose-500 hover:text-rose-650">Cancel</button>
                                </div>
                            {/if}
                        </div>
                    </div>
                {:else}
                    <p class="text-xs text-slate-400 italic">You have not registered any squads to tournaments.</p>
                {/each}
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    {#if showRegisterModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Register to Tournament</h3>
                <p class="text-xs text-slate-450 dark:text-slate-400 mb-4">{selectedEventName} — Division: {selectedEventGame?.game?.name}</p>
                <form onsubmit={submitRegistration} class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="regSquad">Select Squad</label>
                        <select bind:value={squad_id} id="regSquad" required class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white">
                            {#each mySquads.filter(s => s.game_id === selectedEventGame.game_id) as sq}
                                <option value={sq.id}>{sq.name}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl dark:bg-slate-900 text-xs space-y-1 text-slate-500 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span>Ticket Price:</span>
                            <span>Rp {selectedEventGame?.ticket_price || 0}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Admin Fee:</span>
                            <span>Rp {selectedEventGame?.admin_ticket_fee || 0}</span>
                        </div>
                        <div class="flex justify-between font-bold border-t border-slate-200 pt-1 dark:border-slate-800 text-slate-850 dark:text-white mt-1">
                            <span>Total Payment:</span>
                            <span>Rp {(selectedEventGame?.ticket_price || 0) + (selectedEventGame?.admin_ticket_fee || 0)}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick={() => showRegisterModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Submit Entry</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</div>
