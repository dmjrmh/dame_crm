<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Projects</h2>

      <div class="flex items-center gap-2">
        <a href="{{ route('projects.create') }}"
          class="inline-flex items-center rounded-lg bg-emerald-600 hover:bg-white px-4 py-2 text-sm font-medium text-white hover:text-emerald-600 transition shadow-lg">
          + Add New Project
        </a>
      </div>
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
            <option value="{{ $stage->id }}" @selected(request('stage') == $stage->id)>{{ $stage->name }}</option>
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
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Stage</th>
            <th class="px-4 py-3">Amount</th>
            <th class="px-4 py-3 w-48 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($projects as $project)
            <tr>
              <td class="px-4 py-3">
                <span class="font-medium">{{ $project->title }}</span>
              </td>
              <td class="px-4 py-3">{{ $project->customer?->name ?? '-' }}</td>
              <td class="px-4 py-3">
                <span class="rounded-md bg-neutral-100 px-2 py-1 text-xs">
                  {{ $project->pipelineStage?->name ?? '-' }}
                </span>
              </td>
              <td class="px-4 py-3">
                {{ $project->currency }} {{ number_format((float) $project->amount, 2) }}
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('projects.edit', $project) }}" class="underline">Edit</a>
                  <form action="{{ route('projects.destroy', $project) }}" method="POST"
                    onsubmit="return confirm('Delete this deal?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 underline">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-neutral-500">No projects found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $projects->withQueryString()->links() }}
    </div>
  </div>
</x-app-layout>
