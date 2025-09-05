{{-- resources/views/customers/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Customers</h2>
    </div>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">

    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
      <table class="min-w-full text-sm divide-y-2 divide-gray-200">
        <thead class="bg-neutral-50 text-left">
          <tr class="*:font-medium *:text-gray-900">
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Contact</th>
            <th class="px-4 py-3">Company</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Address</th>
            <th class="px-4 py-3 text-center w-28">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($customers as $customer)
            <tr>
              <td class="px-4 py-3">
                <a href="{{ route('customers.show', $customer) }}" class="font-medium hover:underline">
                  {{ $customer->name }}
                </a>
              </td>
              <td class="px-4 py-3">{{ $customer->contact ?? '-' }}</td>
              <td class="px-4 py-3">{{ $customer->company ?? '-' }}</td>
              <td class="px-4 py-3">{{ $customer->email ?? '-' }}</td>
              <td class="px-4 py-3">{{ $customer->address ?? '-' }}</td>
              <td class="px-4 py-3 text-center">
                <a href="{{ route('customers.show', $customer) }}" class="underline">View</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-neutral-500">No customers found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $customers->links() }}
    </div>
  </div>
</x-app-layout>
