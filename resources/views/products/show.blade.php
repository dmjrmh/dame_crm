<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Product Detail</h2>
      <a href="{{ route('products.index') }}" class="text-sm underline hover:no-underline">Back to Products</a>
    </div>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <div class="flow-root">
      <dl class="-my-3 divide-y divide-gray-200 rounded border border-neutral-500 text-sm *:even:bg-gray-50">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Name</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $product->name }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">SKU</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $product->sku }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Unit</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $product->unit }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Harga Pokok Penjualan</dt>
          <dd class="text-gray-700 sm:col-span-2">Rp {{ number_format($product->cost_price,2,',','.') }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Margin %</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ number_format($product->margin_percent,2) }}%</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Harga Jual</dt>
          <dd class="text-gray-700 sm:col-span-2">Rp {{ number_format($product->sell_price,2,',','.') }}</dd>
        </div>
      </dl>
    </div>
  </div>
</x-app-layout>
