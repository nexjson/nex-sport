<script lang="ts">
    // Admin Dashboard Component
</script>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Admin Dashboard</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Overview of pending operations requiring your verification.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Stat Card 1 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Active Tournaments</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">2</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-450 dark:text-slate-400">
                <span>1 ongoing league, 1 in registration</span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-top h-24 w-24 rounded-full bg-amber-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Pending Payments</p>
                    <h3 class="text-2xl font-bold text-amber-500">1</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-amber-500 font-medium">
                <span>Requires deposit verification</span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-top h-24 w-24 rounded-full bg-purple-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Pending Registrations</p>
                    <h3 class="text-2xl font-bold text-purple-500">1</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-purple-550 dark:text-purple-400">
                <span>1 squad waiting organizer approval</span>
            </div>
        </div>
    </div>

    <!-- Verification Section -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Verification Table -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Pending Event Deposits Verification</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-medium text-slate-400">
                            <th class="py-3 font-semibold">Event Name</th>
                            <th class="py-3 font-semibold">Organizer</th>
                            <th class="py-3 font-semibold">Deposit Fee</th>
                            <th class="py-3 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm">
                        <tr>
                            <td class="py-3 font-medium text-slate-900 dark:text-white">Community MLBB Open</td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">NEX Arena Organizer</td>
                            <td class="py-3 text-slate-650 dark:text-slate-300">Rp 1.100.000</td>
                            <td class="py-3">
                                <button class="rounded-lg bg-indigo-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Verify</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">System Alerts</h3>
            <div class="space-y-4">
                <div class="flex gap-3 rounded-xl bg-amber-50/50 p-4 border border-amber-250/20 dark:bg-amber-500/5">
                    <div class="text-amber-500 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-850 dark:text-amber-400">Deposit Unverified</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">"Community MLBB Open" has a pending payment receipt of Rp 1,100,000.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
