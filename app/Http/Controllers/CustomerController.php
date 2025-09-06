<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user  = $request->user();
    $term  = $request->query('query');

    $customers = Customer::query()
      ->when($user->role !== 'manager', fn($query) => $query->where('user_id', $user->id))
      ->when($term, function ($query) use ($term) {
        $like = '%' . $term . '%';
        $query->where(function ($inner) use ($like) {
          $inner->where('name', 'like', $like)
            ->orWhere('contact', 'like', $like)
            ->orWhere('email', 'like', $like)
            ->orWhere('company', 'like', $like)
            ->orWhere('address', 'like', $like);
        });
      })
      ->latest()
      ->paginate(10)
      ->withQueryString();

    return view('customers.index', compact('customers'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Customer $customer)
  {
    $activeItems = $customer->deals()
      ->when(
        Schema::hasColumn('deals', 'approval_status'),
        fn($q) => $q->where(function ($qq) {
          $qq->where('approval_status', 'approved')
            ->orWhereNull('approval_status');
        })
      )
      ->with('items.product')
      ->get()
      ->flatMap(function ($deal) {
        return $deal->items->map(function ($it) use ($deal) {
          return [
            'deal_id'    => $deal->id,
            'deal_title' => $deal->title,
            'product'    => optional($it->product)->name,
            'quantity'   => (int) $it->quantity,
            'unit_price' => (float) $it->unit_price,
            'subtotal'   => (float) $it->quantity * (float) $it->unit_price,
            'started_at' => optional($deal->created_at)->format('Y-m-d H:i'),
          ];
        });
      });

    // ringkasan per product
    $groupedByProduct = $activeItems
      ->groupBy('product')
      ->map(function ($rows) {
        return [
          'product'     => $rows->first()['product'] ?? '-',
          'total_qty'   => (int) $rows->sum('quantity'),
          'avg_price'   => (float) $rows->avg('unit_price'),
          'total_value' => (float) $rows->sum('subtotal'),
        ];
      })
      ->values();

    return view('customers.show', compact('customer', 'activeItems', 'groupedByProduct'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
