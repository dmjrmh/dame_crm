<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Products</h2>
      <a href="{{ route('products.create') }}"
        class="inline-flex items-center rounded-lg bg-emerald-600 hover:bg-white px-4 py-2 text-sm font-medium text-white hover:text-emerald-600 transition shadow-lg">
        + Add New Product
      </a>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">
    {{-- flash --}}
    @if (session('success'))
      <div class="mb-4 rounded-lg bg-emerald-300 p-3 text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    {{-- search/filter --}}
    <form method="GET" class="mb-4">
      <div class="flex gap-3">
        <input type="text" name="query" value="{{ request('query') }}" placeholder="Cari nama produk"
          class="w-full rounded-md border px-3 py-2" />
        <button class="rounded-md border px-4 py-2 bg-yellow-200 hover:bg-neutral-50">Apply</button>
      </div>
    </form>

    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3 text-center">Name</th>
            <th class="px-4 py-3 text-center">SKU</th>
            <th class="px-4 py-3 text-center">Unit</th>
            <th class="px-4 py-3 text-center">HPP</th>
            <th class="px-4 py-3 text-center">Margin Sales</th>
            <th class="px-4 py-3 text-center">Harga Jual</th>
            <th class="px-4 py-3 text-center w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($products as $product)
            <tr>
              <td class="px-4 py-3">
                <a href="{{ route('products.show', $product) }}" class="font-medium hover:underline">
                  {{ $product->name }}
                </a>
              </td>
              <td class="px-4 py-3">{{ $product->sku }}</td>
              <td class="px-4 py-3">{{ $product->unit }}</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($product->cost_price,2,',','.') }}</td>
              <td class="px-4 py-3 text-right">{{ number_format($product->margin_percent,2) }}%</td>
              <td class="px-4 py-3 text-right">Rp {{ number_format($product->sell_price,2,',','.') }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('products.show', $product) }}" class="underline">View</a>
                  <a href="{{ route('products.edit', $product) }}" class="underline">Edit</a>
                  <form action="{{ route('products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-6 text-center text-neutral-500">No products found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $products->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
