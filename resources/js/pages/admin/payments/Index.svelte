<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Payment Verification', href: '/admin/payments' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { payments = [], configs = [], flash = {} }: { payments: any[]; configs: any[]; flash: any } = $props();

    // Active tab: 'payments' or 'fees'
    let activeTab = $state('payments');

    // Service Fee config form state
    let feeConfigs = $state(configs.length > 0 ? JSON.parse(JSON.stringify(configs)) : [
        { min_reward: 0, max_reward: 1000000, service_fee: 50000 }
    ]);

    function verifyPayment(paymentId: number, action: 'approved' | 'rejected') {
        if (confirm(`Are you sure you want to set this payment to ${action}?`)) {
            router.post(`/admin/payments/${paymentId}/verify`, { action });
        }
    }

    function addFeeRow() {
        feeConfigs = [...feeConfigs, { min_reward: 0, max_reward: 0, service_fee: 0 }];
    }

    function removeFeeRow(index: number) {
        feeConfigs = feeConfigs.filter((_, i) => i !== index);
    }

    function saveFeeConfigs() {
        router.post('/admin/payments/fee-config', { configs: feeConfigs });
    }
</script>

<AppHead title="Admin Payments & Fees" />

<div class="space-y-6 p-6 max-w-6xl mx-auto">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Payments & Service Fees</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Verify event deposits and configure the service fee structure.</p>
    </div>

    <!-- Alerts -->
    {#if flash?.success}
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-750 text-sm border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-450 dark:border-emerald-550/20">
            {flash.success}
        </div>
    {/if}
    {#if flash?.error}
        <div class="p-4 rounded-xl bg-rose-50 text-rose-700 text-sm border border-rose-200 dark:bg-rose-500/10 dark:text-rose-450 dark:border-rose-550/20">
            {flash.error}
        </div>
    {/if}

    <!-- Tabs header -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex gap-6 text-sm font-semibold">
        <button
            onclick={() => activeTab = 'payments'}
            class="pb-3 border-b-2 transition-all {activeTab === 'payments' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'}"
        >
            Deposit Payments ({payments.length})
        </button>
        <button
            onclick={() => activeTab = 'fees'}
            class="pb-3 border-b-2 transition-all {activeTab === 'fees' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'}"
        >
            Service Fee Configurations
        </button>
    </div>

    <!-- Tab Contents -->
    {#if activeTab === 'payments'}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-md font-bold text-slate-900 dark:text-white mb-4">Deposit Payments Queue</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-900 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Event Info</th>
                            <th class="py-3 px-4">Organizer</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Method</th>
                            <th class="py-3 px-4">Receipt</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-55 dark:divide-slate-900">
                        {#each payments as pay}
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10">
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                    {pay.event?.name || 'Unknown Event'}
                                </td>
                                <td class="py-3 px-4">{pay.event?.organizer?.name || 'Unknown'}</td>
                                <td class="py-3 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                    Rp {pay.amount.toLocaleString()}
                                </td>
                                <td class="py-3 px-4 uppercase text-xs">{pay.payment_method || '-'}</td>
                                <td class="py-3 px-4 text-xs font-mono">{pay.payment_receipt || '-'}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold capitalize
                                        {pay.status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450' : ''}
                                        {pay.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-450' : ''}
                                        {pay.status === 'rejected' ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/20' : ''}
                                    ">
                                        {pay.status}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    {#if pay.status === 'pending'}
                                        <button onclick={() => verifyPayment(pay.id, 'approved')} class="text-xs bg-emerald-600 text-white hover:bg-emerald-550 px-2.5 py-1 rounded-lg font-semibold">Approve</button>
                                        <button onclick={() => verifyPayment(pay.id, 'rejected')} class="text-xs bg-rose-600 text-white hover:bg-rose-550 px-2.5 py-1 rounded-lg font-semibold">Reject</button>
                                    {:else}
                                        <span class="text-xs text-slate-400 italic">No actions</span>
                                    {/if}
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 italic">No deposit payments found.</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    {:else}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-md font-bold text-slate-900 dark:text-white">Service Fee Matrix</h3>
                    <p class="text-xs text-slate-500">Define the service fee charged to organizers based on the tournament reward tier.</p>
                </div>
                <button onclick={addFeeRow} class="rounded-xl border border-slate-200 hover:bg-slate-55 dark:border-slate-800 px-3 py-1.5 text-xs font-semibold">
                    + Add Config Rule
                </button>
            </div>

            <form onsubmit={saveFeeConfigs} class="space-y-4">
                {#each feeConfigs as config, index}
                    <div class="grid grid-cols-4 gap-4 items-end bg-slate-50/50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-900">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for={`minReward-${index}`}>Min Reward (Rp)</label>
                            <input bind:value={config.min_reward} type="number" id={`minReward-${index}`} required min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for={`maxReward-${index}`}>Max Reward (Rp)</label>
                            <input bind:value={config.max_reward} type="number" id={`maxReward-${index}`} required min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for={`fee-${index}`}>Service Fee (Rp)</label>
                            <input bind:value={config.service_fee} type="number" id={`fee-${index}`} required min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick={() => removeFeeRow(index)} class="text-xs text-rose-500 font-bold hover:underline mb-2.5">
                                Delete Rule
                            </button>
                        </div>
                    </div>
                {/each}

                <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-900">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-semibold text-white hover:bg-indigo-500 transition-colors shadow-sm">
                        Save Configurations
                    </button>
                </div>
            </form>
        </div>
    {/if}
</div>
