<script module lang="ts">
    import { dashboard } from '@/routes';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import SuperAdminDashboard from './dashboard/SuperAdminDashboard.svelte';
    import AdminDashboard from './dashboard/AdminDashboard.svelte';
    import OrganizerDashboard from './dashboard/OrganizerDashboard.svelte';
    import PlayerDashboard from './dashboard/PlayerDashboard.svelte';

    let { auth }: { auth: any } = $props();
</script>

<AppHead title="Dashboard" />

<div class="flex-1 rounded-xl p-4 md:p-6 overflow-y-auto">
    {#if auth?.user?.role === 'super-admin'}
        <SuperAdminDashboard />
    {:else if auth?.user?.role === 'admin'}
        <AdminDashboard />
    {:else if auth?.user?.role === 'organizer'}
        <OrganizerDashboard />
    {:else}
        <PlayerDashboard />
    {/if}
</div>
