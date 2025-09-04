<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = $request->user();

    $leads = Lead::query()
      ->when($user->role !== 'manager', fn($q) => $q->where('user_id', $user->id))

      // Search
      ->when($request->filled('q'), function ($q) use ($request) {
        $term = '%' . $request->query('q') . '%';
        $q->where(function ($inner) use ($term) {
          $inner->where('name', 'like', $term)
            ->orWhere('address', 'like', $term)
            ->orWhere('needs', 'like', $term);
        });
      })

      // Filter status
      ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))

      ->latest()
      ->paginate(10)
      ->withQueryString();

    return view('leads.index', compact('leads'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('leads.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required',
      'contact' => 'required',
      'address' => 'nullable',
      'needs' => 'nullable',
      'status' => 'required|in:New,In Progress,Converted,Follow up,Lost',
    ]);

    $validated['user_id'] = $request->user()->id;

    // dd($validated);

    Lead::create($validated);

    return redirect()->route('leads.index')->with('success', 'Lead berhasil ditambahkan');
  }

  /**
   * Display the specified resource.
   */
  public function show(Lead $lead)
  {
    return view('leads.show', compact('lead'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Lead $lead)
  {
    return view('leads.edit', compact('lead'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Lead $lead)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'contact' => 'required|string|max:255',
      'address' => 'nullable|string|max:255',
      'needs' => 'nullable|string|max:255',
      'status' => 'required|string|in:New,In Progress,Converted,Follow up,Lost',
    ]);

    $lead->update($validated);

    return redirect()
      ->route('leads.index')
      ->with('success', 'Lead updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Lead $lead)
  {
    $this->authorize('delete', $lead);

    $lead->delete();

    return redirect()->route('leads.index')->with('success', 'Lead berhasil di delete');
  }
}
