<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Reporting</h2>

      <a href="{{ route('reports.export', request()->all()) }}" class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-neutral-50">
        Download Excel
      </a>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6 space-y-8">

    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
      <div>
        <label class="block text-sm mb-1">Dari</label>
        <input type="date" name="date_start" value="{{ request('date_start', $start->toDateString()) }}"
          class="w-full rounded-md border px-3 py-2">
      </div>

      <div>
        <label class="block text-sm mb-1">Sampai</label>
        <input type="date" name="date_end" value="{{ request('date_end', $end->toDateString()) }}"
          class="w-full rounded-md border px-3 py-2">
      </div>

      @if ($salesOptions->isNotEmpty())
        <div class="md:col-span-2">
          <label class="block text-sm mb-1">Sales</label>
          <select name="sales_id" class="w-full rounded-md border px-3 py-2">
            <option value="">Semua Sales</option>
            @foreach ($salesOptions as $s)
              <option value="{{ $s->id }}" @selected($salesId == $s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="flex gap-3">
        <button class="rounded-md border px-4 py-2 bg-emerald-300 hover:bg-white hover:text-emerald-600">Terapkan</button>
        <a href="{{ route('reports.index') }}" class="rounded-md border px-4 py-2 bg-yellow-300 hover:text-yellow-600 hover:bg-white">Reset</a>
      </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border p-4">
        <div class="text-sm text-neutral-500">Lead → Customer</div>
        <div class="text-2xl font-semibold">{{ number_format($summary['leads_converted']) }}</div>
      </div>
      <div class="rounded-2xl border p-4">
        <div class="text-sm text-neutral-500">Revenue</div>
        <div class="text-2xl font-semibold text-right">Rp {{ number_format($summary['revenue'], 0) }}</div>
      </div>
      <div class="rounded-2xl border p-4">
        <div class="text-sm text-neutral-500">HPP</div>
        <div class="text-2xl font-semibold text-right">Rp {{ number_format($summary['cost_price'], 0) }}</div>
      </div>
      <div class="rounded-2xl border p-4">
        <div class="text-sm text-neutral-500">Profit</div>
        <div class="text-2xl font-semibold text-right">Rp {{ number_format($summary['profit'], 0) }}</div>
      </div>
    </div>

    @if ($perSales->isNotEmpty())
      <div class="rounded-2xl border overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-neutral-50">
            <tr class="text-left *:px-4 *:py-3">
              <th>Sales</th>
              <th>Lead→Customer</th>
              <th>Revenue</th>
              <th>HPP</th>
              <th>Profit</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach ($perSales as $row)
              <tr class="*:px-4 *:py-2">
                <td>{{ $row->user->name ?? '-' }}</td>
                <td>{{ number_format($row->leads_converted) }}</td>
                <td>Rp {{ number_format($row->revenue, 0) }}</td>
                <td>Rp {{ number_format($row->cost_price, 0) }}</td>
                <td>Rp {{ number_format($row->profit, 0) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <div class="rounded-2xl border overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-neutral-50">
          <tr class="text-left *:px-4 *:py-3">
            <th>Tanggal</th>
            <th>Sales</th>
            <th>Customer</th>
            <th>Deal</th>
            <th>Revenue</th>
            <th>HPP</th>
            <th>Profit</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach ($deals as $deal)
            @php
              $rev = 0;
              $hp = 0;
              foreach ($deal->items as $it) {
                  $rev += (float) $it->unit_price * (int) $it->quantity;
                  $hp += (float) $it->product->cost_price * (int) $it->quantity;
              }
            @endphp
            <tr class="*:px-4 *:py-2">
              <td>{{ $deal->date }}</td>
              <td>{{ $deal->user->name ?? '-' }}</td>
              <td>{{ $deal->customer->name ?? '-' }}</td>
              <td>{{ $deal->title }}</td>
              <td class="text-right">Rp {{ number_format($rev, 0) }}</td>
              <td class="text-right">Rp {{ number_format($hp, 0) }}</td>
              <td class="text-right">Rp {{ number_format($rev - $hp, 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</x-app-layout>
