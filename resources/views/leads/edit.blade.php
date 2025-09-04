<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Edit Lead</h2>
      <a href="{{ route('leads.index') }}" class="text-sm underline hover:no-underline">Back to Leads</a>
    </div>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-4">
      @csrf
      @method('PUT')
      @include('leads._form', [
          'lead' => $lead,
          'submitLabel' => 'Update',
      ])
    </form>
  </div>
</x-app-layout>
