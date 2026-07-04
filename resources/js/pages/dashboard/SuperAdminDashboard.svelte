<script lang="ts">
    // Super Admin Dashboard Component
</script>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Super Admin Dashboard</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Overview of the entire NEX-Sport platform operations.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Stat Card 1 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Users</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">26</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs text-emerald-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="font-medium">+12%</span>
                <span class="text-slate-400">this month</span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-purple-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Events</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">5</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs text-emerald-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="font-medium">+2 new</span>
                <span class="text-slate-400">this week</span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50/70 text-emerald-500 dark:bg-emerald-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Platform Revenue</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">Rp 500.000</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs text-emerald-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="font-medium">+25%</span>
                <span class="text-slate-400">increase</span>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-amber-500/10 blur-xl"></div>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Games</p>
                    <h3 class="text-2xl font-bold text-slate-850 dark:text-white">4</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs text-slate-400">
                <span class="font-medium">3 active</span>
                <span>/ 1 inactive</span>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Activity Log -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">System Activity & Payments Log</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-medium text-slate-400">
                            <th class="py-3 font-semibold">Activity</th>
                            <th class="py-3 font-semibold">User</th>
                            <th class="py-3 font-semibold">Status</th>
                            <th class="py-3 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm">
                        <tr>
                            <td class="py-3 font-medium text-slate-900 dark:text-white">Approved event deposit payment for NEX-Sport MLBB Cup</td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">admin1</td>
                            <td class="py-3"><span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Success</span></td>
                            <td class="py-3 text-slate-400 text-xs">1 month ago</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-900 dark:text-white">Approved event deposit payment for NEX-Sport Football League</td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">admin2</td>
                            <td class="py-3"><span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Success</span></td>
                            <td class="py-3 text-slate-400 text-xs">10 days ago</td>
                        </tr>
                        <tr>
                            <td class="py-3 font-medium text-slate-900 dark:text-white">Pending payment verification for Community MLBB Open</td>
                            <td class="py-3 text-slate-500 dark:text-slate-400">organizer1</td>
                            <td class="py-3"><span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Pending</span></td>
                            <td class="py-3 text-slate-400 text-xs">Recently</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Platform Distribution</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-650 dark:text-slate-300">Players</span>
                        <span class="text-slate-400">20 users</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-2 rounded-full bg-indigo-500" style="width: 77%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-650 dark:text-slate-300">Organizers</span>
                        <span class="text-slate-400">3 users</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-2 rounded-full bg-purple-500" style="width: 12%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-650 dark:text-slate-300">Admins & Super Admin</span>
                        <span class="text-slate-400">3 users</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-2 rounded-full bg-emerald-500" style="width: 11%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
