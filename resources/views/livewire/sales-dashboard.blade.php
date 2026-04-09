<div class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50/50 min-h-screen">

    {{-- Header & Filters --}}
    <div class="flex flex-col gap-4">
        <div class="space-y-1">
            <flux:heading size="xl" level="1">Sales Analytics</flux:heading>
            <flux:subheading>Real-time performance across branches and stores.</flux:subheading>
        </div>

        <div class="flex flex-wrap items-end gap-3">
            <flux:input type="date" wire:model.live="startDate" label="From" class="w-36 sm:w-40" />
            <flux:input type="date" wire:model.live="endDate" label="To" class="w-36 sm:w-40" />
            <flux:select wire:model.live="branchId" label="Branch" class="w-40 sm:w-48" placeholder="All Branches">
                <flux:select.option value="">All Branches</flux:select.option>
                @foreach($branches as $branch)
                    <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:button wire:click="resetFilters" icon="arrow-path" variant="subtle" title="Reset Filters" />
        </div>
    </div>

    {{-- KPI Cards: always 2x2 --}}
    <div class="kpi-grid">
        <flux:card class="space-y-2 border-t-4 border-t-indigo-500">
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-gray-500">Total Revenue</flux:text>
            <flux:heading size="lg" class="truncate">Rp {{ number_format($stats['totalSales'], 0, ',', '.') }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2 border-t-4 border-t-emerald-500">
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-gray-500">Total Transactions</flux:text>
            <flux:heading size="lg">{{ number_format($stats['totalTransactions'], 0, ',', '.') }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2 border-t-4 border-t-amber-500">
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-gray-500">Avg. Order Value</flux:text>
            <flux:heading size="lg" class="truncate">Rp {{ number_format($stats['averageOrderValue'], 0, ',', '.') }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-2 border-t-4 border-t-rose-500">
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-gray-500">Top Performing Store</flux:text>
            <flux:heading size="lg" class="truncate">{{ $stats['topStore'] }}</flux:heading>
        </flux:card>
    </div>

    {{-- Charts Row --}}
    <div class="charts-grid">
        {{-- Revenue Trend Chart
             wire:ignore prevents Livewire from touching the canvas during DOM morphing.
             Chart data arrives via the 'charts-updated' browser event dispatched from PHP. --}}
        <flux:card class="space-y-4">
            <div>
                <flux:heading size="md">Revenue Trend</flux:heading>
                <flux:text size="sm" class="text-gray-500">Daily revenue over selected period</flux:text>
            </div>
            <div wire:ignore class="relative" style="height: 220px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </flux:card>

        {{-- Top Stores Chart --}}
        <flux:card class="space-y-4">
            <div>
                <flux:heading size="md">Top Stores by Revenue</flux:heading>
                <flux:text size="sm" class="text-gray-500">Best performing stores in this period</flux:text>
            </div>
            <div wire:ignore class="relative" style="height: 220px;">
                <canvas id="topStoresChart"></canvas>
            </div>
        </flux:card>
    </div>

    {{-- Main Content Grid --}}
    <div class="main-content-grid">
        {{-- Revenue Distribution by Branch --}}
        <flux:card class="space-y-4">
            <flux:heading size="md">Revenue Distribution by Branch</flux:heading>

            <div class="space-y-5">
                @forelse($stats['revenueByBranch'] as $branchData)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-medium text-gray-700">{{ $branchData->name }}</span>
                            <span class="text-indigo-600 font-semibold">Rp {{ number_format($branchData->total, 0, ',', '.') }}</span>
                        </div>
                        {{-- percentage is capped at 100 server-side, overflow:hidden prevents any edge case --}}
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min($branchData->percentage, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">No data available.</p>
                @endforelse
            </div>
        </flux:card>

        {{-- Sales Table --}}
        <div class="main-content-col2 space-y-4">
            <div class="flex justify-between items-center">
                <flux:heading size="md">Recent Transactions</flux:heading>
                <flux:button variant="primary" icon="plus" wire:click="create">Add New Sale</flux:button>
            </div>

            <flux:card class="!p-0 overflow-hidden">
                <flux:table :paginate="$sales">
                    <flux:table.columns>
                        <flux:table.column>Date</flux:table.column>
                        <flux:table.column>Store / Branch</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($sales as $sale)
                            <flux:table.row :key="$sale->id">
                                <flux:table.cell class="text-gray-500">
                                    {{ $sale->transaction_date->format('d M, H:i') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">{{ $sale->store->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $sale->store->branch->name }}</span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="font-bold text-gray-900">
                                    Rp {{ number_format($sale->amount, 0, ',', '.') }}
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="subtle" icon="pencil-square" wire:click="edit({{ $sale->id }})" />
                                        <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $sale->id }})" wire:confirm="Delete this transaction?" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center py-10 text-gray-400 italic">
                                    No transaction data available for this period.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" class="md:w-[500px] space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditMode ? 'Edit Transaction' : 'Record New Sale' }}</flux:heading>
            <flux:subheading>Please provide the transaction details below.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:select wire:model="storeId" label="Target Store" placeholder="Select a store...">
                @foreach($allStores as $store)
                    <flux:select.option value="{{ $store->id }}">{{ $store->name }} ({{ $store->branch->name }})</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="number" wire:model="amount" label="Sale Amount (IDR)" prepend="Rp" placeholder="0" />

            <flux:input type="datetime-local" wire:model="transactionDate" label="Date & Time" />

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button type="submit" variant="primary">{{ $isEditMode ? 'Save Changes' : 'Record Sale' }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     Chart.js + CSS grid styles – loaded ONCE, survive Livewire re-renders
     ═══════════════════════════════════════════════════════════════════════════ --}}
@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<style>
    /* KPI: always 2-column grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    /* Charts: 1-col mobile, 2-col desktop */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    /* Main content: 1-col mobile, 3-col desktop */
    .main-content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .main-content-col2 { grid-column: span 1; }

    @media (min-width: 1024px) {
        .charts-grid        { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .main-content-grid  { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .main-content-col2  { grid-column: span 2; }
    }
</style>

<script>
(function () {
    /* ─── Chart factory helpers ─────────────────────────────────────────── */
    var COLORS = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

    function formatRp(val) {
        return 'Rp ' + (val >= 1000000
            ? (val / 1000000).toFixed(1) + 'M'
            : val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val);
    }

    function buildTrendChart(labels, values) {
        var el = document.getElementById('revenueTrendChart');
        if (!el) return;
        if (window._trendChart) { window._trendChart.destroy(); window._trendChart = null; }
        window._trendChart = new Chart(el, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: values,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.10)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#6366f1',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (ctx) { return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'); } } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0 } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { maxTicksLimit: 5, callback: formatRp }
                    }
                }
            }
        });
    }

    function buildStoreChart(labels, values) {
        var el = document.getElementById('topStoresChart');
        if (!el) return;
        if (window._storeChart) { window._storeChart.destroy(); window._storeChart = null; }
        window._storeChart = new Chart(el, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: values,
                    backgroundColor: labels.map(function (_, i) { return COLORS[i % COLORS.length]; }),
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (ctx) { return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'); } } }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { maxTicksLimit: 5, callback: formatRp }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    /* ─── Listen for data dispatched by PHP render() ────────────────────── */
    /*
     * $this->dispatch('charts-updated', ...) in Livewire 3 fires a window
     * CustomEvent whose detail matches the named arguments.
     * Because canvases are wrapped in wire:ignore, Livewire never touches them,
     * so the Chart.js instances stay alive between renders and we simply
     * destroy-and-rebuild with fresh data from the event payload.
     */
    window.addEventListener('charts-updated', function (e) {
        var d = e.detail;
        // Livewire 3 wraps named args one level deep: e.detail[0] or e.detail
        var data = Array.isArray(d) ? d[0] : d;

        function tryRender() {
            if (typeof Chart === 'undefined') { setTimeout(tryRender, 100); return; }
            buildTrendChart(data.trendLabels || [], data.trendValues || []);
            buildStoreChart(data.storeLabels || [], data.storeValues || []);
        }
        tryRender();
    });
})();
</script>
@endonce