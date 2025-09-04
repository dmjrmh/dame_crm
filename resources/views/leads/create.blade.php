<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Create Lead</h2>
      <a href="{{ route('leads.index') }}" class="text-sm underline hover:no-underline">Back to Leads</a>
    </div>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <form action="{{ route('leads.store') }}" method="POST" class="space-y-4">
      @csrf
      @include('leads._form', [
          'lead' => null,
          'submitLabel' => 'Create',
      ])
    </form>
  </div>
</x-app-layout>
