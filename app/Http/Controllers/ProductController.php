<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $products = Product::query()
      ->when($request->filled('query'), function ($q) use ($request) {
        $term = '%' . $request->query('query') . '%';
        $q->where(function ($inner) use ($term) {
          $inner->where('name', 'like', $term)
            ->orWhere('sku', 'like', $term)
            ->orWhere('description', 'like', $term);
        });
      })->orderByDesc('id')
      ->paginate(10)
      ->withQueryString();

    return view('products.index', compact('products'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('products.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|',
      'sku' => 'required|unique:products,sku',
      'unit' => 'required',
      'cost_price' => 'required|numeric|min:0',
      'margin_percent' => 'required|numeric|min:0',
      'sell_price' => 'required|numeric|min:0',
      'description' => 'nullable',
    ]);

    if ($data['cost_price'] > 0) {
      $data['sell_price'] = round($data['cost_price'] * (1 + $data['margin_percent'] / 100), 2);
    }

    Product::create($data);

    return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan');
  }

  /**
   * Display the specified resource.
   */
  public function show(Product $product)
  {
    return view('products.show', compact('product'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Product $product)
  {
    return view('products.edit', compact('product'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Product $product)
  {
    $data = $request->validate([
      'name' => 'required',
      'sku'  => [ 'required','string','max:100', Rule::unique('products', 'sku')->ignore($product->id)->whereNull('deleted_at'),],
      'unit' => 'required',
      'cost_price' => 'required|numeric|min:0',
      'margin_percent' => 'required|numeric|min:0',
      'sell_price' => 'required|numeric|min:0',
      'description' => 'nullable',
    ]);

    if ($data['cost_price'] > 0) {
      $data['sell_price'] = round($data['cost_price'] * (1 + $data['margin_percent'] / 100), 2);
    }

    $product->update($data);

    return redirect()->route('products.index')->with('success', 'Product updated.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Product $product)
  {
    $product->delete(); // soft delete
    return back()->with('success', 'Product deleted.');
  }
}
