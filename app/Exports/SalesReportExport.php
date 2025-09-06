<?php

namespace App\Exports;

use App\Models\Deal;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
  protected $user;
  protected $salesId;
  protected $start;
  protected $end;

  protected $summary = [
    'lead_to_customer' => 0,
    'revenue' => 0,
    'hpp' => 0,
    'profit' => 0,
  ];

  public function __construct($user, $salesId, $start, $end)
  {
    $this->user    = $user;
    $this->salesId = $salesId;
    $this->start   = Carbon::parse($start)->startOfDay();
    $this->end     = Carbon::parse($end)->endOfDay();
  }

  public function collection()
  {
    $deals = Deal::query()
      ->when($this->user->role !== 'manager', fn($q) => $q->where('user_id', $this->user->id))
      ->when($this->user->role === 'manager' && $this->salesId, fn($q) => $q->where('user_id', $this->salesId))
      ->whereBetween('created_at', [$this->start, $this->end])
      ->with(['customer:id,name', 'user:id,name', 'items.product:id,cost_price'])
      ->get();

    $this->summary['lead_to_customer'] = $deals->whereNotNull('customer_id')->count();
    $this->summary['revenue'] = $deals->sum('amount');
    $this->summary['hpp'] = $deals->sum(function ($d) {
      return $d->items->sum(function ($it) {
        $qty   = (int) ($it->quantity ?? 1);
        $cost  = $it->cost_price ?? $it->product->cost_price ?? 0;
        return $qty * (float) $cost;
      });
    });
    $this->summary['profit'] = $this->summary['revenue'] - $this->summary['hpp'];

    return $deals;
  }

  public function map($deal): array
  {
    $hpp = $deal->items->sum(function ($it) {
      $qty  = (int) ($it->quantity ?? 1);
      $cost = $it->cost_price ?? $it->product->cost_price ?? 0;
      return $qty * (float) $cost;
    });

    $profit = (float) $deal->amount - $hpp;

    return [
      optional($deal->created_at)->toDateString(),
      $deal->user->name ?? '-',
      $deal->customer->name ?? '-',
      $deal->title,
      (float) $deal->amount,
      $hpp,
      $profit,
    ];
  }

  public function headings(): array
  {
    return [['Tanggal', 'Sales', 'Customer', 'Deal', 'Revenue', 'HPP', 'Profit']];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet;
        $row = $sheet->getHighestRow() + 2;
        $sheet->setCellValue("A{$row}", 'Summary');
        $sheet->setCellValue("B{$row}", 'Lead → Customer');
        $sheet->setCellValue("C{$row}", $this->summary['lead_to_customer']);

        $sheet->setCellValue("E{$row}",   'Revenue');
        $sheet->setCellValue("F{$row}",   $this->summary['revenue']);
        $sheet->setCellValue("E" . ($row + 1), 'HPP');
        $sheet->setCellValue("F" . ($row + 1), $this->summary['hpp']);
        $sheet->setCellValue("E" . ($row + 2), 'Profit');
        $sheet->setCellValue("F" . ($row + 2), $this->summary['profit']);
      },
    ];
  }
}
