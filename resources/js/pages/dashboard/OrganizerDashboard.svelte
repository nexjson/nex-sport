<script lang="ts">
    // Organizer Dashboard Component
</script>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Organizer Dashboard</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Manage your tournaments, registrations, and match brackets.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Stat Card 1 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-purple-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">My Tournaments</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">3</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-450 dark:text-slate-400">
                <span>1 completed, 1 waiting payment, 1 draft</span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Registered Participants</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">8 Squads</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-450 dark:text-slate-400">
                <span>Across MLBB and PUBGM events</span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Ticket Revenue</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">Rp 150.000</h3>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-450 dark:text-slate-400">
                <span>Total payouts from ticket sales</span>
            </div>
        </div>
    </div>

    <!-- Tournaments List -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">My Tournaments List</h3>
            <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">Create Tournament</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-medium text-slate-400">
                        <th class="py-3 font-semibold">Tournament Name</th>
                        <th class="py-3 font-semibold">Game Category</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">Start Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm">
                    <tr>
                        <td class="py-3 font-medium text-slate-900 dark:text-white">NEX-Sport MLBB Cup</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">Mobile Legends</td>
                        <td class="py-3"><span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">Completed</span></td>
                        <td class="py-3 text-slate-400 text-xs">1 month ago</td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-slate-900 dark:text-white">Community MLBB Open</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">Mobile Legends</td>
                        <td class="py-3"><span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Waiting Verification</span></td>
                        <td class="py-3 text-slate-400 text-xs">In 20 days</td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-slate-900 dark:text-white">Organizer 2 MLBB Tournament</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">Mobile Legends</td>
                        <td class="py-3"><span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-400">Draft</span></td>
                        <td class="py-3 text-slate-400 text-xs">In 30 days</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
