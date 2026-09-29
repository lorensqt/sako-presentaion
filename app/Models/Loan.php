<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'type_key',
        'name',
        'partner',
        'loanable_amount',
        'fixed_deposit',
        'comakers',
        'interest_rate',
        'available_terms',
        'max_term_months',
        'minimum_membership_months',
        'hrmd_approval',
        'is_active',
        'approval_flow',
        'metadata',
    ];

    protected $casts = [
        'comakers' => 'json',
        'fixed_deposit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'available_terms' => 'array',
        'is_active' => 'boolean',
        'hrmd_approval' => 'boolean',
        'approval_flow' => 'json',
        'metadata' => 'json',
    ];

    /**
     * Determine if this loan product has custom configured terms and rates.
     */
    public function hasCustomTerms(): bool
    {
        return !empty($this->available_terms) && is_array($this->available_terms) && count($this->available_terms) > 0;
    }

    /**
     * Retrieve the sorted array of available terms.
     * Each element contains ['months' => int, 'interest_rate' => float].
     */
    public function getSortedTerms(): array
    {
        if (!$this->hasCustomTerms()) {
            return [];
        }

        $terms = $this->available_terms;
        usort($terms, fn($a, $b) => ($a['months'] ?? 0) <=> ($b['months'] ?? 0));

        return $terms;
    }

    /**
     * Get the specific interest rate configured for a given tenure in months.
     * Falls back to the product's base interest_rate if not found or no custom terms exist.
     */
    public function getInterestRateForTerm(int $months): float
    {
        if ($this->hasCustomTerms()) {
            foreach ($this->available_terms as $term) {
                if (isset($term['months']) && (int) $term['months'] === $months) {
                    return (float) ($term['interest_rate'] ?? $this->interest_rate);
                }
            }
        }

        return (float) $this->interest_rate;
    }

    /**
     * Check if a specific term in months is allowed for this loan product.
     */
    public function isTermAllowed(int $months): bool
    {
        if (!$this->hasCustomTerms()) {
            $max = $this->max_term_months ?? 60;
            return $months >= 1 && $months <= $max;
        }

        foreach ($this->available_terms as $term) {
            if (isset($term['months']) && (int) $term['months'] === $months) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the applications associated with this loan product.
     */
    public function applications()
    {
        return $this->hasMany(LoanApplication::class);
    }
}
