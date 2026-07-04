<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'My Tournaments', href: '/organizer/events' },
            { title: 'Create Tournament', href: '/organizer/events/create' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { games = [] }: { games: any[] } = $props();

    let name = $state('');
    let description = $state('');
    let start_date = $state('');
    let end_date = $state('');

    function submit() {
        router.post('/organizer/events', {
            name,
            description,
            start_date,
            end_date
        });
    }

    function cancel() {
        router.visit('/organizer/events');
    }
</script>

<AppHead title="Create Tournament" />

<div class="max-w-2xl mx-auto p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Create New Tournament</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Step 1: Set up basic tournament details and schedule.</p>
    </div>

    <form onsubmit={submit} class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eName">Tournament Name</label>
            <input bind:value={name} type="text" id="eName" required placeholder="e.g. NEX-Sport Pro League MLBB" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="eDesc">Description & Rules</label>
            <textarea bind:value={description} id="eDesc" rows="4" placeholder="Rules, bracket format, prizepool distribution..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white"></textarea>
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

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-900">
            <button type="button" onclick={cancel} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
            <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save & Continue</button>
        </div>
    </form>
</div>
