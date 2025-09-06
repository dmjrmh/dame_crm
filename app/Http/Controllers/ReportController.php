<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
  public function index(Request $request)
  {
    $user  = $request->user();

    $start = $request->date_start
      ? Carbon::parse($request->date_start)->startOfDay()
      : now()->startOfMonth();
    $end   = $request->date_end
      ? Carbon::parse($request->date_end)->endOfDay()
      : now()->endOfDay();

    $salesId = $user->role === 'manager' ? $request->sales_id : $user->id;

    $deals = Deal::query()
      ->when($user->role !== 'manager', fn($q) => $q->where('user_id', $user->id))
      ->when($user->role === 'manager' && $salesId, fn($q) => $q->where('user_id', $salesId))
      ->whereBetween('date', [$start, $end])
      ->with(['items.product:id,cost_price,sell_price', 'user:id,name', 'customer:id,name'])
      ->get();

    // ---- AGREGASI ----
    $summary = [
      'leads_converted' => $deals->whereNotNull('customer_id')->count(),
      'revenue'         => 0,
      'cost_price'      => 0, // <— ganti dari hpp
      'profit'          => 0,
    ];

    foreach ($deals as $deal) {
      foreach ($deal->items as $item) {
        $qty = (int) $item->quantity;
        $summary['revenue']    += (float) $item->unit_price * $qty;
        $summary['cost_price'] += (float) optional($item->product)->cost_price * $qty;
      }
    }
    $summary['profit'] = $summary['revenue'] - $summary['cost_price'];

    // ---- RINGKASAN PER SALES (manager) ----
    $perSales = collect();
    if ($user->role === 'manager') {
      $perSales = Deal::query()
        ->selectRaw('user_id')
        ->selectRaw("COUNT(DISTINCT CASE WHEN customer_id IS NOT NULL THEN id END) as leads_converted")
        ->selectRaw("
          COALESCE(SUM((
            SELECT SUM(di.quantity * di.unit_price)
            FROM deal_items di
            WHERE di.deal_id = deals.id AND di.deleted_at IS NULL
          )),0) as revenue
        ")
        ->selectRaw("
          COALESCE(SUM((
            SELECT SUM(di.quantity * p.cost_price)
            FROM deal_items di
            JOIN products p ON p.id = di.product_id
            WHERE di.deal_id = deals.id AND di.deleted_at IS NULL
          )),0) as cost_price
        ")
        ->when($salesId, fn($q) => $q->where('user_id', $salesId))
        ->whereBetween('date', [$start, $end])
        ->groupBy('user_id')
        ->with('user:id,name')
        ->get()
        ->map(function ($row) {
          $row->profit = (float) $row->revenue - (float) $row->cost_price;
          return $row;
        });
    }

    $salesOptions = $user->role === 'manager'
      ? User::query()->where('role', '!=', 'manager')->orderBy('name')->get(['id', 'name'])
      : collect();

    return view('reports.index', compact('summary', 'deals', 'perSales', 'salesOptions', 'start', 'end', 'salesId'));
  }

  public function export(Request $request)
  {
    $user    = $request->user();
    $salesId = $user->role === 'manager' ? $request->sales_id : $user->id;
    $start   = $request->date_start ?: now()->startOfMonth()->toDateString();
    $end     = $request->date_end   ?: now()->toDateString();

    return Excel::download(
      new SalesReportExport($user, $salesId, $start, $end),
      'sales-report-' . now()->format('Ymd_His') . '.xlsx'
    );
  }
}
