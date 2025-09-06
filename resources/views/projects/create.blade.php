<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight">New Project</h2>
  </x-slot>

  <div class="max-w-6xl mx-auto p-6">
    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
      @csrf
      <div>
        <label class="block text-sm mb-1">Title</label>
        <input name="title" class="w-full rounded-md border px-3 py-2" required>
        @error('title')
          <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Source: Lead or Customer --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm mb-1">Source</label>
          <select id="source_type" name="source_type" class="w-full rounded-md border px-3 py-2" required>
            <option value="">—</option>
            <option value="lead">Lead</option>
            <option value="customer">Customer</option>
          </select>
        </div>

        <div id="lead_group" style="display:none">
          <label class="block text-sm mb-1">Lead</label>
          <select id="lead_id" name="lead_id" class="w-full rounded-md border px-3 py-2">
            <option value="">—</option>
            @foreach ($leads as $lead)
              <option value="{{ $lead->id }}">{{ $lead->name }} — Phone: {{ $lead->contact }}</option>
            @endforeach
          </select>
        </div>

        <div id="customer_group" style="display:none">
          <label class="block text-sm mb-1">Customer</label>
          <select id="customer_id" name="customer_id" class="w-full rounded-md border px-3 py-2">
            <option value="">—</option>
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm mb-1">Stage</label>
          <select name="pipeline_stage_id" class="w-full rounded-md border px-3 py-2" required>
            @foreach ($stages as $stage)
              <option value="{{ $stage->id }}">{{ $stage->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm mb-1">Expected Close Date</label>
          <input name="expected_close_date" type="date" class="rounded-md border px-3 py-2"
            value="{{ date('Y-m-d') }}">
        </div>

        <div class="col-span-full">
          <label for="notes" class="block text-sm/6 font-medium text-gray-900">Notes</label>
          <div class="mt-2">
            <textarea id="notes" name="notes" rows="3"
              placeholder="Contoh: Butuh internet cepat untuk kantor, minimal 100 Mbps"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ old('notes', $lead->notes ?? '') }}</textarea>
          </div>
          @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="rounded-xl border">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h3 class="font-semibold">Deal Items</h3>
          <button type="button" id="add-item"
            class="rounded-md bg-neutral-900 text-white px-3 py-2 hover:bg-neutral-800">+ Add Item</button>
        </div>

        <div id="items-wrapper" class="divide-y">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 items-start deal-row">
            <div class="md:col-span-4">
              <label class="block text-sm mb-1">Product</label>
              <select name="items[0][product_id]" class="product w-full rounded-md border px-3 py-2" required>
                <option value="">—</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}" data-price="{{ (float) $product->sell_price }}">
                    {{ $product->name }} (IDR {{ number_format((float) $product->sell_price, 2) }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm mb-1">Qty</label>
              <input type="number" name="items[0][quantity]" min="1" value="1"
                class="qty text-right w-full rounded-md border px-3 py-2">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm mb-1">Unit Price</label>
              <input type="number" step="0.01" min="0" name="items[0][unit_price]"
                class="unit-price w-full rounded-md border px-3 py-2 text-right">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm mb-1">Total</label>
              <input type="number" step="0.01" min="0"
                class="sub-total-price w-full rounded-md border px-3 py-2 text-right" readonly>
            </div>

            <div class="md:col-span-2 pt-8">
              <button type="button" class="text-red-600 underline remove-item">Remove</button>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 py-3 border-t flex items-center justify-end">
        <div class="text-sm text-neutral-500 text-right mr-2">Grand Total</div>
        <input id="amount" name="amount" type="number" step="0.01" min="0"
          class="text-right rounded-md border px-3 py-2" readonly>
      </div>
      @error('amount')
        <p class="text-red-600 text-sm text-right">{{ $message }}</p>
      @enderror

      <div class="pt-2 flex justify-end">
        <button type="submit"
          class="rounded-md bg-neutral-900 text-white px-4 py-2 hover:bg-neutral-800">Save</button>
        <a href="{{ route('projects.index') }}"
          class="ms-2 rounded-md border px-4 py-2 hover:bg-neutral-50">Cancel</a>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // --- Source (Lead/Customer) toggle ---
      function toggleSourceUI() {
        const srcEl = document.getElementById('source_type');
        const leadGp = document.getElementById('lead_group');
        const custGp = document.getElementById('customer_group');
        const leadEl = document.getElementById('lead_id');
        const custEl = document.getElementById('customer_id');
        if (!srcEl || !leadEl || !custEl || !leadGp || !custGp) return;

        const v = srcEl.value;
        if (v === 'lead') {
          leadGp.style.display = '';
          custGp.style.display = 'none';
          leadEl.disabled = false;
          leadEl.required = true;
          custEl.disabled = true;
          custEl.required = false;
          custEl.value = '';
        } else if (v === 'customer') {
          leadGp.style.display = 'none';
          custGp.style.display = '';
          custEl.disabled = false;
          custEl.required = true;
          leadEl.disabled = true;
          leadEl.required = false;
          leadEl.value = '';
        } else {
          leadGp.style.display = 'none';
          custGp.style.display = 'none';
          leadEl.disabled = true;
          leadEl.required = false;
          leadEl.value = '';
          custEl.disabled = true;
          custEl.required = false;
          custEl.value = '';
        }
      }
      const srcEl = document.getElementById('source_type');
      if (srcEl) {
        srcEl.addEventListener('change', toggleSourceUI);
        toggleSourceUI();
      }

      // --- Totals helpers ---
      const wrapper = document.getElementById('items-wrapper');
      const amountInput = document.getElementById('amount');

      const toNum = (v) => {
        const n = Number(v);
        return Number.isFinite(n) ? n : 0;
      };

      function updateRowSubtotal(row) {
        const qtyEl = row.querySelector('.qty');
        const unitEl = row.querySelector('.unit-price');
        const subEl = row.querySelector('.sub-total-price');
        const qty = toNum(qtyEl?.value);
        const unit = toNum(unitEl?.value);
        const subtotal = qty * unit;
        if (subEl) subEl.value = subtotal;
      }

      function recalcGrandTotal() {
        let total = 0;
        document.querySelectorAll('#items-wrapper .deal-row').forEach(row => {
          const subEl = row.querySelector('.sub-total-price');
          total += toNum(subEl?.value);
        });
        if (amountInput) amountInput.value = total;
      }

      function recalcAll() {
        document.querySelectorAll('#items-wrapper .deal-row').forEach(updateRowSubtotal);
        recalcGrandTotal();
      }

      // Prefill unit price when product changes
      document.addEventListener('change', (e) => {
        if (e.target && e.target.classList?.contains('product')) {
          const row = e.target.closest('.deal-row');
          const unitInput = row?.querySelector('.unit-price');
          const selected = e.target.selectedOptions?.[0];
          const price = selected ? Number(selected.dataset.price || 0) : 0;
          if (unitInput) unitInput.value = price;
          updateRowSubtotal(row);
          recalcGrandTotal();
        }
      });

      // Recalculate on qty / unit input
      document.addEventListener('input', (e) => {
        if (!e.target) return;
        if (e.target.classList?.contains('qty') || e.target.classList?.contains('unit-price')) {
          const row = e.target.closest('.deal-row');
          updateRowSubtotal(row);
          recalcGrandTotal();
        }
      });

      // Add item row (with Subtotal column)
      const addBtn = document.getElementById('add-item');
      let itemIndex = 1;
      if (addBtn && wrapper) {
        addBtn.addEventListener('click', () => {
          const html = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 items-start deal-row">
              <div class="md:col-span-4">
                <label class="block text-sm mb-1 md:hidden">Product</label>
                <select name="items[${itemIndex}][product_id]" class="product w-full rounded-md border px-3 py-2" required>
                  <option value="">—</option>
                  @foreach ($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ (float) $p->sell_price }}">
                      {{ $p->name }} (IDR {{ number_format((float) $p->sell_price, 2) }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="md:col-span-1">
                <label class="block text-sm mb-1 md:hidden">Qty</label>
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="qty text-right w-full rounded-md border px-3 py-2">
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm mb-1 md:hidden">Unit Price</label>
                <input type="number" step="0.01" min="0" name="items[${itemIndex}][unit_price]"
                  class="unit-price w-full rounded-md border px-3 py-2 text-right">
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm mb-1 md:hidden">Total</label>
                <input type="number" step="0.01" min="0"
                  class="sub-total-price w-full rounded-md border px-3 py-2 text-right" readonly>
              </div>

              <div class="md:col-span-2 pt-8">
                <button type="button" class="text-red-600 underline remove-item">Remove</button>
              </div>
            </div>`;
          wrapper.insertAdjacentHTML('beforeend', html);
          itemIndex++;
        });
      }

      // Remove item
      document.addEventListener('click', (e) => {
        if (e.target && e.target.classList?.contains('remove-item')) {
          const row = e.target.closest('.deal-row');
          if (row) row.remove();
          recalcGrandTotal();
        }
      });

      // initial calc
      recalcAll();
    });
  </script>

</x-app-layout>
