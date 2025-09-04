<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Lead Detail</h2>
      <a href="{{ route('leads.index') }}" class="text-sm underline hover:no-underline">Back to Leads</a>
    </div>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <div class="flow-root">
      <dl class="-my-3 divide-y divide-gray-200 rounded border border-neutral-500 text-sm *:even:bg-gray-50">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Name</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $lead->name }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Contact</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $lead->contact }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Address</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $lead->address }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Needs</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ $lead->needs }}</dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Status</dt>
          <dd class="text-gray-700 sm:col-span-2">{{ ucfirst($lead->status) }}</dd>
        </div>
      </dl>
    </div>
  </div>
</x-app-layout>
