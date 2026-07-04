<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Prizepool Payouts', href: '/player/claims' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';

    let { claims = [], flash = {} }: { claims: any[]; flash: any } = $props();

    let showClaimModal = $state(false);
    let selectedClaim = $state<any>(null);

    // Form fields
    let bank_name = $state('');
    let account_number = $state('');
    let account_name = $state('');

    function openClaim(claim: any) {
        selectedClaim = claim;
        bank_name = '';
        account_number = '';
        account_name = '';
        showClaimModal = true;
    }

    function submitClaim() {
        router.post(`/player/claims/${selectedClaim.id}/claim`, {
            bank_name,
            account_number,
            account_name
        }, {
            onSuccess: () => showClaimModal = false
        });
    }
</script>

<AppHead title="Prizepool Payouts" />

<div class="space-y-6 p-6 max-w-4xl mx-auto">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Prizepool Payout Claims</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Withdraw tournament earnings and view gateway transfer receipts.</p>
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

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Pending Claims -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Pending Claims</h3>
            <div class="space-y-4">
                {#each claims.filter(c => c.status === 'pending' || c.status === 'failed') as claim}
                    <div class="p-4 border border-slate-100 rounded-xl dark:border-slate-900 space-y-2 bg-amber-50/10 border-amber-200/20">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{claim.reward?.event_game?.event?.name}</h4>
                                <p class="text-xs text-slate-400">Reward: {claim.reward?.title} (Prizepool: Rp {claim.reward?.prize_amount})</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 uppercase">{claim.status}</span>
                        </div>
                        {#if claim.status === 'failed'}
                            <p class="text-xs text-rose-550 font-semibold mt-1">Disbursement failed. Please verify bank credentials and retry.</p>
                        {/if}
                        <div class="pt-2 flex justify-end border-t border-slate-100 dark:border-slate-900">
                            <button onclick={() => openClaim(claim)} class="rounded-lg bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-500">Claim Reward</button>
                        </div>
                    </div>
                {:else}
                    <p class="text-sm text-slate-400 italic">No pending payout claims available.</p>
                {/each}
            </div>
        </div>

        <!-- Settled Payments -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Payout History</h3>
            <div class="space-y-4">
                {#each claims.filter(c => c.status === 'paid' || c.status === 'processing') as claim}
                    <div class="p-4 border border-slate-100 rounded-xl dark:border-slate-900 space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{claim.reward?.event_game?.event?.name}</h4>
                                <p class="text-xs text-slate-400">Reward: {claim.reward?.title} (Prizepool: Rp {claim.reward?.prize_amount})</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 uppercase">{claim.status}</span>
                        </div>
                        <div class="pt-2 text-xs text-slate-450 dark:text-slate-400 border-t border-slate-100 dark:border-slate-900">
                            <p>Bank: {claim.bank_name} • A/C: {claim.account_number}</p>
                            <p class="mt-1 font-mono text-[10px] text-slate-400">Receipt: {claim.payment_receipt || '-'}</p>
                        </div>
                    </div>
                {:else}
                    <p class="text-sm text-slate-400 italic">No prize payout history yet.</p>
                {/each}
            </div>
        </div>
    </div>
</div>

<!-- Bank Form Modal -->
{#if showClaimModal}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 dark:bg-slate-950 dark:border-slate-850">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Claim Prize Payout</h3>
            <p class="text-xs text-slate-400 mb-4">Tournament: {selectedClaim?.reward?.event_game?.event?.name}</p>
            <form onsubmit={submitClaim} class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="bkName">Bank Name</label>
                    <input bind:value={bank_name} type="text" id="bkName" required placeholder="e.g. Bank Mandiri, BCA" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="bkNum">Account Number</label>
                    <input bind:value={account_number} type="text" id="bkNum" required placeholder="e.g. 12400012345 (use 99999 to simulate fail)" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1" for="bkOwner">Account Holder Name</label>
                    <input bind:value={account_name} type="text" id="bkOwner" required placeholder="Name matching bank account record" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-white" />
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick={() => showClaimModal = false} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Request Transfer</button>
                </div>
            </form>
        </div>
    </div>
{/if}
