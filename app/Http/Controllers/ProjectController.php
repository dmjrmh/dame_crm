<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PipelineStage;
use Illuminate\Support\Carbon;
use App\Models\CustomerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\DealApprovalService;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;

class ProjectController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = $request->user();
    $q = $request->q;

    $projects = Deal::with('customer:id,name', 'pipelineStage:id,name')
      ->when($user->role !== 'manager', fn($q2) => $q2->where('user_id', $user->id))
      ->when($q, function ($query) use ($q) {
        $term = "%$q%";
        $query->where(function ($qq) use ($term) {
          $qq->where('title', 'like', $term)
            ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $term));
        });
      })
      ->latest()->paginate(10)->withQueryString();

    $stages = PipelineStage::orderBy('order')->get(['id', 'name']);

    return view('projects.index', compact('projects', 'stages'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Request $request)
  {
    $user = $request->user();

    $leads = Lead::query()
      ->when($user->role !== 'manager', fn($q) => $q->where('user_id', $user->id))
      ->where('status', '!=', 'Converted')
      ->orderBy('name')
      ->get(['id', 'name', 'contact']);

    $customers = Customer::query()
      ->when($user->role !== 'manager', fn($q) => $q->where('user_id', $user->id))
      ->orderBy('name')
      ->get(['id', 'name']);

    $stages = PipelineStage::orderBy('order')->get(['id', 'name']);
    $products = Product::orderBy('name')->get(['id', 'name', 'sell_price', 'cost_price']);

    return view('projects.create', compact('leads', 'customers', 'stages', 'products'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreDealRequest $request, DealApprovalService $approval)
  {
    $user = $request->user();

    return DB::transaction(function () use ($request, $user, $approval) {
      $deal = Deal::create([
        'user_id'            => $user->id,
        'title'              => $request->title,
        'date'               => $request->date,
        'customer_id'        => $request->customer_id,
        'lead_id'            => $request->lead_id,
        'amount'             => $request->amount,
        'expected_close_date' => $request->expected_close_date,
        'pipeline_stage_id'  => $request->pipeline_stage_id,
        'approval_status'    => 'none',
        'notes'              => $request->input('notes'),
      ]);

      // items
      $products = Product::whereIn('id', collect($request->items)->pluck('product_id'))->get()->keyBy('id');
      $items = collect($request->items)->map(fn($r) => [
        'product_id' => $r['product_id'],
        'quantity'   => (int)$r['quantity'],
        'unit_price' => (float)$r['unit_price'],
        'sell_price' => (float)$products[$r['product_id']]->sell_price, // snapshot
        'cost_price' => (float)($products[$r['product_id']]->cost_price ?? 0),
      ]);
      $deal->items()->createMany($items->all());

      $decision = $approval->evaluate($deal, $items->map(fn($r) => $r + ['product' => $products[$r['product_id']]]));
      $deal->fill($decision)->save();

      $deal->load('pipelineStage', 'lead');

      if ($deal->lead) {
        if ($deal->pipelineStage?->is_closed) {
          if ($deal->pipelineStage->is_won) {
            $deal->lead->update(['status' => 'Converted']);

            if (is_null($deal->customer_id)) {
              $customer = Customer::create([
                'user_id'  => $deal->user_id,
                'lead_id'  => $deal->lead_id,
                'name'     => $deal->lead->name,
                'contact'  => $deal->lead->contact,
                'address'  => $deal->lead->address,
                'notes'    => 'Auto-converted from Closed–Won deal #' . $deal->id,
              ]);
              $deal->update(['customer_id' => $customer->id]);
            }
          } else {
            $deal->lead->update(['status' => 'Lost']);
          }
        } else {
          $deal->lead->update(['status' => 'In Progress']);
        }
      }

      return redirect()->route('projects.index')
        ->with('success', $deal->approval_status === 'approved'
          ? 'Deal auto-approved & Won 🎉'
          : 'Deal menunggu approval manager.');
    });
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
  public function edit(Deal $project)
  {
    return view('projects.edit', [
      'deal'       => $project->load('items', 'pipelineStage', 'customer', 'lead'),
      'stages'     => PipelineStage::orderBy('order')->get(),
      'leads'      => Lead::orderBy('name')->get(),
      'customers'  => Customer::orderBy('name')->get(),
      'products'   => Product::orderBy('name')->get(),
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update()
  {
    return back()->with('error', 'Fitur edit belum tersedia.');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Deal $project)
  {
    DB::transaction(function () use ($project) {
      $lead = $project->lead;

      $project->items()->delete();

      $project->delete();

      if ($lead) {
        $hasActive = $lead->deals()
          ->whereNull('deleted_at')
          ->exists();

        if (!$hasActive && $lead->status !== 'Converted') {
          $lead->update(['status' => 'Follow up']);
        }
      }
    });

    return back()->with('success', 'Deal deleted.');
  }

  public function approvals(Request $request)
  {
    $stages = PipelineStage::orderBy('order')->get();

    $status = 'pending';

    $deals = Deal::query()
      ->with(['user', 'lead', 'customer', 'pipelineStage'])
      ->where('approval_status', $status)
      ->whereNotIn('pipeline_stage_id', [4, 5])
      ->when($request->query('query'), function ($q) use ($request) {
        $term = '%' . $request->query('query') . '%';
        $q->where(function ($inner) use ($term) {
          $inner->where('title', 'like', $term)
            ->orWhereHas('customer', fn($c) => $c->where('name', 'like', $term))
            ->orWhereHas('lead', fn($l) => $l->where('name', 'like', $term));
        });
      })
      ->when($request->query('stage'), function ($q) use ($request) {
        $q->where('pipeline_stage_id', $request->stage);
      })
      ->latest()
      ->paginate(10);

    return view('projects.approvals', compact('deals', 'stages'));
  }

  public function approve(Request $request, Deal $deal)
  {
    $request->validate([
      'decision' => 'required|in:approved,rejected',
      'note'     => 'nullable|string',
    ]);

    DB::transaction(function () use ($request, $deal) {
      if ($request->decision === 'approved') {
        $wonStageId = PipelineStage::where('is_closed', true)
          ->where('is_won', true)
          ->value('id');

        if (!$deal->customer_id && $deal->lead) {
          $customer = Customer::firstOrCreate(
            ['lead_id' => $deal->lead_id],
            [
              'user_id' => $deal->user_id,
              'name'    => $deal->lead->name,
              'contact' => $deal->lead->contact,
              'email'   => $deal->lead->email,
              'address' => $deal->lead->address,
            ]
          );
          $deal->customer_id = $customer->id;
        }

        $deal->update([
          'approval_status'   => 'approved',
          'approved_at'       => now(),
          'approver_id'       => Auth::id(),
          'pipeline_stage_id' => $wonStageId,
          'notes'             => trim(($deal->notes ?? '') . "\n\n[Manager Approval] " . ($request->note ?? '')),
        ]);

        if ($deal->lead) {
          $deal->lead->update(['status' => 'Converted']);
        }
      } else {
        $lostStageId = PipelineStage::where('is_closed', true)
          ->where('is_won', false)
          ->value('id') ?? PipelineStage::where('key', 'lost')->value('id');

        $deal->update([
          'approval_status'   => 'rejected',
          'approved_at'       => null,
          'approver_id'       => Auth::id(),
          'pipeline_stage_id' => $lostStageId,
          'closed_at'         => now(),
          'notes'             => trim(($deal->notes ?? '') . "\n\n[Manager Rejection] " . ($request->note ?? '')),
        ]);

        if ($deal->lead) {
          $hasOtherActive = $deal->lead->deals()
            ->whereNull('deleted_at')
            ->where('id', '!=', $deal->id)
            ->exists();

          if (!$hasOtherActive) {
            $deal->lead->update(['status' => 'Lost']);
          }
        }
      }
    });

    return back()->with('success', 'Decision saved.');
  }
}
