<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Customer Detail</h2>
      <a href="{{ route('customers.index') }}" class="text-sm underline hover:no-underline">Back to Customers</a>
    </div>
  </x-slot>

  <div class="max-w-4xl mx-auto p-6">
    <h3 class="text-lg font-semibold mb-2">Customer Info</h3>
    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3 w-48">Field</th>
            <th class="px-4 py-3">Value</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr>
            <td class="px-4 py-3">Name</td>
            <td class="px-4 py-3">{{ $customer->name }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Contact</td>
            <td class="px-4 py-3">{{ $customer->contact ?? '-' }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Company</td>
            <td class="px-4 py-3">{{ $customer->company ?? '-' }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Email</td>
            <td class="px-4 py-3">{{ $customer->email ?? '-' }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Address</td>
            <td class="px-4 py-3">{{ $customer->address ?? '-' }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Notes</td>
            <td class="px-4 py-3">{{ $customer->notes ?? '-' }}</td>
          </tr>
          <tr>
            <td class="px-4 py-3">Created At</td>
            <td class="px-4 py-3">{{ optional($customer->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="max-w-4xl mx-auto p-6">
    <h3 class="text-lg font-semibold mb-2">Active Services</h3>
    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3">Deal</th>
            <th class="px-4 py-3">Product</th>
            <th class="px-4 py-3 text-right">Qty</th>
            <th class="px-4 py-3 text-right">Unit Price</th>
            <th class="px-4 py-3 text-right">Subtotal</th>
            <th class="px-4 py-3">Started At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($activeItems as $row)
            <tr>
              <td class="px-4 py-3">{{ $row['deal_title'] }}</td>
              <td class="px-4 py-3">{{ $row['product'] ?? '-' }}</td>
              <td class="px-4 py-3 text-right">{{ number_format($row['quantity'], 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($row['unit_price'], 0, ',', '.') }}</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($row['subtotal'], 0, ',', '.') }}</td>
              <td class="px-4 py-3">{{ $row['started_at'] }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-neutral-500">Belum ada layanan aktif.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($groupedByProduct->isNotEmpty())
      <h3 class="text-lg font-semibold mt-8 mb-2">Service Summary by Product</h3>
      <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
        <table class="min-w-full text-sm divide-y-2 divide-gray-200">
          <thead class="bg-neutral-50 text-left">
            <tr class="*:font-medium *:text-gray-900">
              <th class="px-4 py-3">Product</th>
              <th class="px-4 py-3 text-right">Total Qty</th>
              <th class="px-4 py-3 text-right">Avg Unit Price</th>
              <th class="px-4 py-3 text-right">Total Value</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach ($groupedByProduct as $item)
              <tr>
                <td class="px-4 py-3">{{ $item['product'] ?? '-' }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($item['total_qty'], 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($item['avg_price'], 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($item['total_value'], 0, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</x-app-layout>
