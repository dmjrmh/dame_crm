@php
  $p = $product ?? new \App\Models\Product();
@endphp

<div x-data="productCalc({
    hpp: {{ old('cost_price', $p->cost_price ?? 0) }},
    margin: {{ old('margin_percent', $p->margin_percent ?? 0) }},
    jual: {{ old('sell_price', $p->sell_price ?? 0) }},
})" x-init="recalcFromHPP()" class="space-y-4">

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
      <label class="block text-sm font-medium">Name</label>
      <input name="name" value="{{ old('name', $p->name) }}" required class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('name')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">SKU</label>
      <input name="sku" value="{{ old('sku', $p->sku) }}" required
        class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('sku')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
      <label class="block text-sm font-medium">Unit</label>
      <input name="unit" value="{{ old('unit', $p->unit ?? 'pcs') }}"
        class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('unit')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
      <label class="block text-sm font-medium">HPP (cost)</label>
      <input name="cost_price" type="number" step="0.01" min="0" x-model.number="hpp"
        @input="recalcFromHPP()" class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('cost_price')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Margin (%)</label>
      <input name="margin_percent" type="number" step="0.01" min="0" x-model.number="margin"
        @input="recalcFromHPP()" class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('margin_percent')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Harga Jual</label>
      <input name="sell_price" type="number" step="0.01" min="0" x-model.number="jual"
        @input.debounce.300ms="recalcFromSell()" class="mt-1 w-full rounded-md border px-3 py-2" />
      @error('sell_price')
        <p class="text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium">Description</label>
    <textarea name="description" rows="4" class="mt-1 w-full rounded-md border px-3 py-2">{{ old('description', $p->description) }}</textarea>
    @error('description')
      <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div class="flex items-center justify-end gap-2">
    <a href="{{ route('products.index') }}" class="rounded-md border px-4 py-2 hover:bg-neutral-50">Cancel</a>
    <button class="rounded-md bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
      {{ $submitLabel ?? 'Save' }}
    </button>
  </div>
</div>

{{-- Alpine helpers: dua arah kalkulasi --}}
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('productCalc', (initial) => ({
      hpp: initial.hpp ?? 0,
      margin: initial.margin ?? 0,
      jual: initial.jual ?? 0,
      recalcFromHPP() {
        const h = Number(this.hpp) || 0;
        const m = Number(this.margin) || 0;
        this.jual = +(h * (1 + m / 100)).toFixed(2);
      },
      recalcFromSell() {
        const h = Number(this.hpp) || 0;
        const s = Number(this.jual) || 0;
        this.margin = h > 0 ? +(((s - h) / h) * 100).toFixed(2) : 0;
      }
    }));
  });
</script>
