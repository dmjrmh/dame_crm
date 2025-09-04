@csrf

<div class="space-y-12">
  {{-- SECTION: Lead Information --}}
  <div class="border-b border-gray-900/10 pb-12">
    <h2 class="text-base/7 font-semibold text-gray-900">Lead Information</h2>
    <p class="mt-1 text-sm/6 text-gray-600">Isi data calon pelanggan secara lengkap.</p>

    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
      {{-- Name --}}
      <div class="sm:col-span-3">
        <label for="name" class="block text-sm/6 font-medium text-gray-900">Name <span
            class="text-red-500">*</span></label>
        <div class="mt-2">
          <input id="name" name="name" type="text" required value="{{ old('name', $lead->name ?? '') }}"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
        </div>
        @error('name')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Contact number --}}
      <div class="sm:col-span-3">
        <label for="contact" class="block text-sm/6 font-medium text-gray-900">Contact number</label>
        <div class="mt-2">
          <input id="contact" name="contact" type="text" inputmode="tel"
            value="{{ old('contact', $lead->contact ?? '') }}"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
        </div>
        @error('contact')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Address --}}
      <div class="col-span-full">
        <label for="address" class="block text-sm/6 font-medium text-gray-900">Address</label>
        <div class="mt-2">
          <textarea id="address" name="address" rows="3"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ old('address', $lead->address ?? '') }}</textarea>
        </div>
        @error('address')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Needs (3 rows) --}}
      <div class="col-span-full">
        <label for="needs" class="block text-sm/6 font-medium text-gray-900">Needs</label>
        <div class="mt-2">
          <textarea id="needs" name="needs" rows="3"
            placeholder="Contoh: Butuh internet cepat untuk kantor, minimal 100 Mbps"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ old('needs', $lead->needs ?? '') }}</textarea>
        </div>
        @error('needs')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Status --}}
      @php
        $statuses = ['New', 'Follow up', 'In Progress', 'Converted', 'Lost'];
      @endphp
      <div class="sm:col-span-3">
        <label for="status" class="block text-sm/6 font-medium text-gray-900">Status</label>
        <div class="mt-2 grid grid-cols-1">
          <select id="status" name="status"
            class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pl-3 pr-8 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
            <option @selected(old('status', $lead->status ?? null) === null)>Select status</option>
            @foreach ($statuses as $status)
              <option value="{{ $status }}" @selected(old('status', $lead->status ?? 'new') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}
              </option>
            @endforeach
          </select>
          <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"
            class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4">
            <path
              d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" />
          </svg>
        </div>
        @error('status')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

    </div>
  </div>
</div>

{{-- Actions --}}
<div class="mt-6 flex items-center justify-end gap-x-6">
  <a href="{{ route('leads.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
  <button type="submit"
    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
    {{ $submitLabel ?? 'Save' }}
  </button>
</div>
