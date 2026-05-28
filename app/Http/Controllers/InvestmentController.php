<?php

namespace App\Http\Controllers;

use App\Events\InvestmentRequested;
use App\Events\InvestmentStatusChanged;
use App\Models\Investment;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInvestmentRequest;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Investment::class);

        $user = auth()->user();

        $investments = Investment::with(['startup.founder', 'investor'])
            ->when($user->role === User::ROLE_STUDENT_FOUNDER, function ($q) use ($user) {
                $q->whereHas('startup', fn ($s) => $s->where('founder_id', $user->id));
            })
            ->when($user->role === User::ROLE_INVESTOR, fn ($q) => $q->where('investor_id', $user->id))
            ->when($user->role === User::ROLE_ADMIN, fn ($q) => $q)
            ->latest()
            ->paginate(12);

        // For students, also get unique investors who invested in their startups
        $investors = collect();
        if ($user->role === User::ROLE_STUDENT_FOUNDER) {
            $investors = User::whereHas('investments', function ($q) use ($user) {
                $q->whereHas('startup', fn ($s) => $s->where('founder_id', $user->id))
                  ->where('status', 'approved');
            })->get();
        }

        return view('investments.index', compact('investments', 'investors'));
    }

    public function show(Investment $investment)
    {
        $this->authorize('view', $investment);

        $investment->load(['startup.founder', 'investor']);
        return view('investments.show', compact('investment'));
    }

    public function create(Request $request, ?Startup $startup = null)
    {
        $this->authorize('create', Investment::class);

        if ($request->filled('startup_id') && ! $startup) {
            $startup = Startup::with('founder')->findOrFail($request->startup_id);
        }

        $startups = Startup::with('founder')->orderBy('name')->get();

        $existingInvestment = null;
        if ($startup) {
            $existingInvestment = Investment::where('investor_id', auth()->id())
                ->where('startup_id', $startup->id)
                ->first();
        }

        return view('investments.create', compact('startups', 'startup', 'existingInvestment'));
    }

    public function store(StoreInvestmentRequest $request)
    {
        $existing = Investment::where('investor_id', auth()->id())
            ->where('startup_id', $request->startup_id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return back()
                    ->withInput()
                    ->with('error', 'You already have a pending offer for this startup.');
            }

            if ($existing->status === 'approved') {
                return back()
                    ->withInput()
                    ->with('error', 'Your investment in this startup is already approved.');
            }

            if ($existing->status === 'rejected') {
                $existing->update([
                    'amount' => $request->amount,
                    'message' => $request->message,
                    'status' => 'pending',
                    'admin_remarks' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);

                event(new InvestmentRequested($existing->fresh()));

                return redirect()->route('investments.show', $existing)
                    ->with('success', 'Your new offer was sent to the founder for review.');
            }
        }

        $data = $request->validated();
        $data['investor_id'] = auth()->id();
        $data['status'] = 'pending';

        $investment = Investment::create($data);
        $investment->load(['startup.founder', 'investor']);

        event(new InvestmentRequested($investment));

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Your investment offer was sent to the founder. They will review and respond soon.');
    }

    public function approve(Request $request, Investment $investment)
    {
        $this->authorize('approve', $investment);

        $request->validate(['admin_remarks' => ['nullable', 'string', 'max:1000']]);

        $oldStatus = $investment->status;
        $investment->update([
            'status' => 'approved',
            'admin_remarks' => $request->admin_remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        event(new InvestmentStatusChanged($investment, $oldStatus));

        return redirect()->route('investments.show', $investment)->with('success', 'Investment approved. The investor has been notified.');
    }

    public function reject(Request $request, Investment $investment)
    {
        $this->authorize('approve', $investment);

        $request->validate(['admin_remarks' => ['nullable', 'string', 'max:1000']]);

        $oldStatus = $investment->status;
        $investment->update([
            'status' => 'rejected',
            'admin_remarks' => $request->admin_remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        event(new InvestmentStatusChanged($investment, $oldStatus));

        return redirect()->route('investments.show', $investment)->with('success', 'Investment offer declined.');
    }
}