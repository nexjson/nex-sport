<script lang="ts">
    // Player Dashboard Component
</script>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Player Dashboard</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Track your squad, schedules, and past match results.</p>
    </div>

    <!-- Main Player Grid -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Side: Squad Info & Stats -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Squad Info Card -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">My Squad Division</h3>
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center border border-slate-200 dark:border-slate-800">
                        <svg class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-md">RRQ Hoshi</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Mobile Legends: Bang Bang</p>
                        <p class="text-xs font-semibold text-indigo-500 mt-1">Status: Active</p>
                    </div>
                </div>
            </div>

            <!-- Squad Stats Card -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Division Stats</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl">
                        <p class="text-2xl font-bold text-slate-850 dark:text-white">3</p>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Wins</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl">
                        <p class="text-2xl font-bold text-slate-850 dark:text-white">1</p>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Losses</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl">
                        <p class="text-2xl font-bold text-slate-850 dark:text-white">75%</p>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Winrate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Matches Sched & History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Next Match Card -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Upcoming Squad Match</h3>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-4 rounded-xl bg-gradient-to-r from-indigo-500/10 via-purple-500/5 to-transparent border border-indigo-500/10">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div>
                            <span class="text-xs font-semibold text-indigo-500">NEX-Sport Football League</span>
                            <h4 class="text-sm font-bold text-slate-850 dark:text-white mt-0.5">RRQ FC vs ONIC FC</h4>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Live Now</p>
                        <p class="text-xs text-slate-400">NEX Stadium Jakarta</p>
                    </div>
                </div>
            </div>

            <!-- Match History Card -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Recent Match Results</h3>
                <div class="divide-y divide-slate-100 dark:divide-slate-900">
                    <!-- Match 1 -->
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-850 dark:text-white">RRQ Hoshi vs ONIC Esports</p>
                            <p class="text-xs text-slate-400">NEX-Sport MLBB Cup (Final)</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-emerald-500">3 - 2</span>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Won</span>
                        </div>
                    </div>
                    <!-- Match 2 -->
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-850 dark:text-white">RRQ Hoshi vs EVOS Glory</p>
                            <p class="text-xs text-slate-400">NEX-Sport MLBB Cup (Semi-Final)</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-emerald-500">2 - 1</span>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Won</span>
                        </div>
                    </div>
                    <!-- Match 3 -->
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-850 dark:text-white">RRQ FC vs EVOS FC</p>
                            <p class="text-xs text-slate-400">NEX-Sport Football League</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-amber-500">1 - 1</span>
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Draw</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
