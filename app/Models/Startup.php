<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Startup extends Model
{
    public const INDUSTRY_CATEGORIES = [
        'CleanTech',
        'AgriTech',
        'HealthTech',
        'EdTech',
        'FinTech',
        'Logistics',
        'FoodTech',
        'AI/ML',
        'E-Commerce',
        'Cybersecurity',
    ];

    protected $fillable = [
        'founder_id',
        'name',
        'description',
        'industry',
        'stage',
        'logo',
        'website',
        'team_size',
        'pitch_deck_id',
        'is_verified',
        'status',
        'is_flagged',
        'flag_reason',
        'verified_by',
        'verified_at',
        'funding_goal',
        'amount_raised',
        'equity_offered',
        'problem',
        'solution',
        'market_opportunity',
        'business_model',
        'traction',
        'vision',
        'mission',
        'active_users',
        'mrr',
        'growth_rate',
        'burn_rate',
        'runway_months',
        'funding_deadline',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_flagged' => 'boolean',
            'verified_at' => 'datetime',
            'funding_goal' => 'decimal:2',
            'amount_raised' => 'decimal:2',
            'equity_offered' => 'decimal:2',
            'mrr' => 'decimal:2',
            'growth_rate' => 'decimal:2',
            'burn_rate' => 'decimal:2',
            'funding_deadline' => 'datetime',
        ];
    }

    public function founder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'founder_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function pitchDeck(): BelongsTo
    {
        return $this->belongsTo(PitchDeck::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function approvedInvestments(): HasMany
    {
        return $this->hasMany(Investment::class)->where('status', 'approved');
    }

    public function scopeIdea($query)
    {
        return $query->where('stage', 'idea');
    }

    public function scopeMvp($query)
    {
        return $query->where('stage', 'mvp');
    }

    public function scopeFunded($query)
    {
        return $query->where('stage', 'funded');
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('industry', 'like', "%{$term}%")
              ->orWhere('mission', 'like', "%{$term}%");
        });
    }

    public function scopeFilterByStage($query, ?string $stage)
    {
        if (blank($stage)) return $query;
        return $query->where('stage', $stage);
    }

    public function scopeFilterByIndustry($query, ?string $industry)
    {
        if (blank($industry)) return $query;
        return $query->where('industry', $industry);
    }

    /**
     * Group startups into ordered industry sections for display.
     */
    public static function groupByIndustry(iterable $startups): array
    {
        $grouped = collect($startups)->groupBy(fn ($s) => $s->industry ?: 'Other');
        $ordered = [];

        foreach (self::INDUSTRY_CATEGORIES as $category) {
            if ($grouped->has($category)) {
                $ordered[$category] = $grouped[$category]->values();
            }
        }

        foreach ($grouped as $category => $items) {
            if (! isset($ordered[$category])) {
                $ordered[$category] = $items->values();
            }
        }

        return $ordered;
    }

    public static function industryIcon(string $industry): string
    {
        return match ($industry) {
            'CleanTech' => 'fa-leaf',
            'AgriTech' => 'fa-seedling',
            'HealthTech' => 'fa-heart-pulse',
            'EdTech' => 'fa-graduation-cap',
            'FinTech' => 'fa-coins',
            'Logistics' => 'fa-truck',
            'FoodTech' => 'fa-utensils',
            'AI/ML' => 'fa-robot',
            'E-Commerce' => 'fa-cart-shopping',
            'Cybersecurity' => 'fa-shield-halved',
            default => 'fa-building',
        };
    }

    public function scopeTrending($query)
    {
        return $query->withCount(['investments as investor_count' => function ($q) {
            $q->select(DB::raw('count(distinct investor_id)'));
        }])
        ->orderByDesc('investor_count')
        ->orderByDesc('amount_raised');
    }

    public function scopeRecentlyFunded($query)
    {
        return $query->where('amount_raised', '>', 0)
            ->orderByDesc('updated_at');
    }

    public function calculateProgressScore(): int
    {
        $score = 0;
        if ($this->name) $score += 15;
        if ($this->description) $score += 15;
        if ($this->industry) $score += 10;
        if ($this->logo) $score += 10;
        if ($this->website) $score += 10;
        if ($this->team_size && $this->team_size > 1) $score += 10;
        if ($this->mission) $score += 10;
        if ($this->problem) $score += 10;
        if ($this->solution) $score += 10;
        return min(100, $score);
    }

    public function getFundingPercentAttribute(): int
    {
        if (!$this->funding_goal || $this->funding_goal <= 0) return 0;
        $raised = $this->amount_raised ?? 0;
        return min(100, (int) round(($raised / $this->funding_goal) * 100));
    }

    public function getInvestorCountAttribute(): int
    {
        return $this->investments()
            ->whereIn('status', ['approved', 'pending'])
            ->distinct('investor_id')
            ->count('investor_id');
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->funding_deadline) return null;
        return now()->diffInDays($this->funding_deadline, false);
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=184343&color=E3D2C0&size=128&font-size=0.4';
    }

    public function getFormattedFundingGoalAttribute(): string
    {
        return $this->formatMoney($this->funding_goal);
    }

    public function getFormattedAmountRaisedAttribute(): string
    {
        return $this->formatMoney($this->amount_raised);
    }

    public function getFormattedMrrAttribute(): ?string
    {
        return $this->mrr ? $this->formatMoney($this->mrr) : null;
    }

    public function getFormattedBurnRateAttribute(): ?string
    {
        return $this->burn_rate ? $this->formatMoney($this->burn_rate) : null;
    }

    private function formatMoney(?float $amount): string
    {
        if (!$amount) return '$0';
        if ($amount >= 1000000) {
            return '$' . number_format($amount / 1000000, 2) . 'M';
        }
        if ($amount >= 1000) {
            return '$' . number_format($amount / 1000, 1) . 'K';
        }
        return '$' . number_format($amount);
    }
}
