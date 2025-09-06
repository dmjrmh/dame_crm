<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Approvals</h2>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">
    @if (session('success'))
      <div class="mb-4 rounded-lg bg-emerald-300 p-3 text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    <form method="GET" class="mb-4">
      <div class="flex gap-3">
        <input type="text" name="query" value="{{ request('query') }}" placeholder="Cari title/customer…"
          class="w-full rounded-md border px-3 py-2" />

        <select name="stage" class="rounded-md border px-3 py-2">
          <option value="">All stages</option>
          @foreach ($stages as $stage)
            <option value="{{ $stage->id }}" @selected(request('stage') == $stage->id)>
              {{ $stage->name }}
            </option>
          @endforeach
        </select>

        <button class="rounded-md border px-4 py-2 bg-yellow-200 hover:bg-neutral-50">Apply</button>
      </div>
    </form>

    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Customer/Lead</th>
            <th class="px-4 py-3">Stage</th>
            <th class="px-4 py-3">Amount</th>
            <th class="px-4 py-3">Approval</th>
            <th class="px-4 py-3 text-center w-56">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($deals as $deal)
            <tr>
              <td class="px-4 py-3 font-medium">{{ $deal->title }}</td>

              <td class="px-4 py-3">
                {{ $deal->customer->name ?? ($deal->lead->name ?? '-') }}
              </td>

              <td class="px-4 py-3">
                <span class="rounded-md bg-neutral-100 px-2 py-1 text-xs">
                  {{ $deal->pipelineStage?->name ?? '-' }}
                </span>
              </td>

              <td class="px-4 py-3">
                IDR {{ number_format((float) $deal->amount, 0) }}
              </td>

              <td class="px-4 py-3">
                <span
                  class="rounded-md px-2 py-1 text-xs
                  @if ($deal->approval_status === 'pending') bg-yellow-100 text-yellow-800
                  @elseif($deal->approval_status === 'approved') bg-emerald-100 text-emerald-800
                  @elseif($deal->approval_status === 'rejected') bg-red-100 text-red-700
                  @else bg-neutral-100 text-neutral-700 @endif">
                  {{ ucfirst($deal->approval_status) }}
                </span>
              </td>

              <td class="px-4 py-3 text-center">
                {{-- Approve Button --}}
                <button onclick="document.getElementById('approve-{{ $deal->id }}').showModal()"
                  class="text-emerald-700 underline">Approve</button>

                {{-- Reject Button --}}
                <button onclick="document.getElementById('reject-{{ $deal->id }}').showModal()"
                  class="text-red-600 underline ml-2">Reject</button>
              </td>
            </tr>

            {{-- APPROVE MODAL --}}
            <dialog id="approve-{{ $deal->id }}" class="rounded-lg p-6 max-w-lg w-full">
              <form method="POST" action="{{ route('projects.approve', $deal) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="decision" value="approved">

                <h3 class="text-lg font-semibold">Approve Deal</h3>
                <p class="text-sm text-neutral-600">{{ $deal->title }}</p>

                <div>
                  <label class="block text-sm mb-1">Catatan (opsional)</label>
                  <textarea name="note" rows="3" class="w-full rounded-md border px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                  <button type="button" onclick="this.closest('dialog').close()"
                    class="rounded-md border px-4 py-2">Batal</button>
                  <button type="submit"
                    class="rounded-md bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Approve</button>
                </div>
              </form>
            </dialog>

            {{-- REJECT MODAL --}}
            <dialog id="reject-{{ $deal->id }}" class="rounded-lg p-6 max-w-lg w-full">
              <form method="POST" action="{{ route('projects.approve', $deal) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="decision" value="rejected">

                <h3 class="text-lg font-semibold">Reject Deal</h3>
                <p class="text-sm text-neutral-600">{{ $deal->title }}</p>

                <div>
                  <label class="block text-sm mb-1">Alasan / Catatan</label>
                  <textarea name="note" rows="3" class="w-full rounded-md border px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                  <button type="button" onclick="this.closest('dialog').close()"
                    class="rounded-md border px-4 py-2">Batal</button>
                  <button type="submit"
                    class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">Reject</button>
                </div>
              </form>
            </dialog>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-neutral-500">Tidak ada deal untuk approval.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $deals->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
