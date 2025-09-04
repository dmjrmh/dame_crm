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
      $leads = Lead::query()
      ->when($request->q, function ($query) use ($request) {
        $query->where(function ($inner) use ($request) {
          $inner->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')
                ->orWhere('address', 'like', '%'.$request->q.'%');
        });
      })
      ->when($request->status, function ($query) use ($request){
        $query->where('status', $request->status);
      })
      ->latest()
      ->paginate(10);

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
        'needs' => 'nullable'
      ]);

      $validated['user_id'] = $request->user()->id;

      Lead::create($validated);

      return redirect()->route('leads.index')->with('success', 'Lead berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(Lead $lead)
    {
      $this->authorize('delete', $lead);

      $lead->delete();

      return redirect()->route('leads.index')->with('success', 'Lead berhasil di delete');
    }
}
