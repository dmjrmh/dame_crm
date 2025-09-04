<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight">Edit Product</h2>
      <a href="{{ route('products.index') }}" class="text-sm underline hover:no-underline">Back to Products</a>
    </div>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-4">
      @csrf
      @method('PUT')
      @include('products._form', [
          'product' => $product,
          'submitLabel' => 'Update',
      ])
    </form>
  </div>
</x-app-layout>
