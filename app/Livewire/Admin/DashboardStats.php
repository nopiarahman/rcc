<?php

namespace App\Livewire\Admin;

use App\Models\Pesanan;
use App\Models\PesananDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        $today     = today();
        $year      = now()->year;
        $month     = now()->month;

        $todayOrders  = Pesanan::whereDate('created_at', $today)->count();
        $todayRevenue = Pesanan::whereDate('created_at', $today)
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        $monthOrders  = Pesanan::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        $monthRevenue = Pesanan::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));

        $chartLabels  = $days->map(fn ($d) => $d->format('d/m'))->values();
        $chartOrders  = $days->map(fn ($d) =>
            Pesanan::whereDate('created_at', $d)->where('status', '!=', 'dibatalkan')->count()
        )->values();
        $chartRevenue = $days->map(fn ($d) =>
            (int) Pesanan::whereDate('created_at', $d)->where('status', '!=', 'dibatalkan')->sum('total_harga')
        )->values();

        $topMinuman = PesananDetail::select('nama_minuman', DB::raw('SUM(qty) as total_qty'))
            ->whereNotNull('nama_minuman')
            ->where('nama_minuman', '!=', '')
            ->groupBy('nama_minuman')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topMakanan = PesananDetail::select('nama_makanan', DB::raw('SUM(qty) as total_qty'))
            ->whereNotNull('nama_makanan')
            ->where('nama_makanan', '!=', '')
            ->groupBy('nama_makanan')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $statusCounts = Pesanan::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('livewire.admin.dashboard-stats', compact(
            'todayOrders', 'todayRevenue', 'monthOrders', 'monthRevenue',
            'chartLabels', 'chartOrders', 'chartRevenue',
            'topMinuman', 'topMakanan', 'statusCounts'
        ));
    }
}
