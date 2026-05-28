<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\Startup;
use App\Models\User;

class InvestmentService
{
    public function __construct(
        private NotificationService $notification,
    ) {}

    public function expressInterest(User $investor, Startup $startup, array $data): Investment
    {
        $investment = Investment::create([
            'investor_id' => $investor->id,
            'startup_id' => $startup->id,
            'status' => 'pending',
            'amount' => $data['amount'] ?? null,
            'message' => $data['message'] ?? null,
        ]);

        // Notify the founder
        $this->notification->notify(
            $startup->founder,
            'success',
            'New Investment Offer',
            "{$investor->name} expressed interest in {$startup->name}" . ($data['amount'] ? ' for ' . $startup->formattedFundingGoal : ''),
            route('investments.show', $investment)
        );

        return $investment;
    }

    public function approve(Investment $investment, User $reviewer, ?string $remarks = null): void
    {
        $investment->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_remarks' => $remarks,
        ]);

        // Update startup amount_raised
        if ($investment->amount) {
            $startup = $investment->startup;
            $startup->amount_raised = ($startup->amount_raised ?? 0) + $investment->amount;
            $startup->save();
        }

        // Notify both parties
        $this->notification->notify(
            $investment->investor,
            'success',
            'Investment Approved',
            "Your investment in {$investment->startup->name} has been approved.",
            route('investments.show', $investment)
        );

        $this->notification->notify(
            $investment->startup->founder,
            'success',
            'Investment Approved',
            "Investment from {$investment->investor->name} has been approved.",
            route('investments.show', $investment)
        );
    }

    public function reject(Investment $investment, User $reviewer, ?string $remarks = null): void
    {
        $investment->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_remarks' => $remarks,
        ]);

        $this->notification->notify(
            $investment->investor,
            'warning',
            'Investment Declined',
            "Your investment in {$investment->startup->name} was not approved.",
            route('investments.show', $investment)
        );
    }

    public function withdraw(Investment $investment, User $actor): void
    {
        $investment->update(['status' => 'withdrawn']);

        $this->notification->notify(
            $investment->startup->founder,
            'info',
            'Investment Withdrawn',
            "{$actor->name} withdrew their investment offer for {$investment->startup->name}",
            route('investments.show', $investment)
        );
    }

    public function getUserInvestments(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Investment::where('investor_id', $user->id)
            ->with(['startup', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStartupInvestments(Startup $startup): \Illuminate\Database\Eloquent\Collection
    {
        return Investment::where('startup_id', $startup->id)
            ->with(['investor', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getPendingReview(): \Illuminate\Database\Eloquent\Collection
    {
        return Investment::pending()
            ->with(['investor', 'startup.founder', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();
    }
}