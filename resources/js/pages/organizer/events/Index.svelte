<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'My Tournaments', href: '/organizer/events' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { events = [], flash = {} }: { events: any[]; flash: any } = $props();

    function createEvent() {
        router.visit('/organizer/events/create');
    }

    function editEvent(id: number) {
        router.visit(`/organizer/events/${id}/edit`);
    }

    function deleteEvent(id: number) {
        if (confirm('Are you sure you want to delete this tournament draft?')) {
            router.delete(`/organizer/events/${id}`);
        }
    }
</script>

<AppHead title="My Tournaments" />

<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tournament Organizer Panel</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Launch tournaments, configure ticket costs, and track brackets.</p>
        </div>
        <button
            onclick={createEvent}
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-300"
        >
            Create New Tournament
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

    <!-- Event Lists Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {#each events as event}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight">{event.name}</h3>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider
                            {event.status === 'draft' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400' : ''}
                            {event.status === 'waiting_verification' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : ''}
                            {event.status === 'registration' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : ''}
                            {event.status === 'ongoing' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : ''}
                            {event.status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : ''}
                        ">
                            {event.status}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-450 dark:text-slate-400 mb-4">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{new Date(event.start_date).toLocaleDateString()} - {new Date(event.end_date).toLocaleDateString()}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{event.event_games?.length || 0} Game Divisions</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 mb-4">{event.description || 'No description provided.'}</p>
                </div>

                <div class="mt-4 flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-900">
                    <button onclick={() => editEvent(event.id)} class="text-xs font-semibold text-indigo-650 dark:text-indigo-400 hover:underline">Manage / Setup</button>
                    {#if event.status === 'draft'}
                        <button onclick={() => deleteEvent(event.id)} class="text-xs font-semibold text-rose-600 dark:text-rose-455 hover:underline">Delete Draft</button>
                    {/if}
                </div>
            </div>
        {:else}
            <div class="md:col-span-2 lg:col-span-3 rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center dark:border-slate-800">
                <p class="text-sm text-slate-400 italic mb-4">You have not launched any tournaments yet.</p>
                <button onclick={createEvent} class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">Launch First Tournament</button>
            </div>
        {/each}
    </div>
</div>
