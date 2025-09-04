<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Leads</h2>
      <a href="{{ route('leads.create') }}"
        class="inline-flex items-center rounded-lg bg-emerald-600 hover:bg-white px-4 py-2 text-sm font-medium text-white hover:text-emerald-600 transition shadow-lg">
        + Add New Lead
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
        <input type="text" name="query" value="{{ request('query') }}" placeholder="Cari nama/kontak/alamat…"
          class="w-full rounded-md border px-3 py-2" />
        <select name="status" class="rounded-md border px-3 py-2">
          <option value="">All status</option>
          @foreach (['new', 'follow_up', 'in_progress', 'converted', 'lost'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
          @endforeach
        </select>
        <button class="rounded-md border px-4 py-2 bg-yellow-200 hover:bg-neutral-50">Apply</button>
      </div>
    </form>

    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Contact</th>
            <th class="px-4 py-3">Address</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($leads as $lead)
            <tr>
              <td class="px-4 py-3">
                <a href="{{ route('leads.show', $lead) }}" class="font-medium hover:underline">
                  {{ $lead->name }}
                </a>
              </td>
              <td class="px-4 py-3">{{ $lead->contact }}</td>
              <td class="px-4 py-3">{{ $lead->address }}</td>
              <td class="px-4 py-3">
                <span class="rounded-md bg-neutral-100 px-2 py-1 text-xs">{{ ucfirst($lead->status) }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('leads.show', $lead) }}" class="underline">View</a>
                  <a href="{{ route('leads.edit', $lead) }}" class="underline">Edit</a>
                  <form action="{{ route('leads.destroy', $lead) }}" method="POST"
                    onsubmit="return confirm('Delete this lead?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-neutral-500">No leads found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $leads->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
