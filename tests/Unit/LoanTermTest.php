<?php

namespace Tests\Unit;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTermTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_model_handles_available_terms_and_custom_interest_rates(): void
    {
        $loan = Loan::create([
            'category' => 'regular',
            'type_key' => 'custom_multi_term_loan',
            'name' => 'Multi-Term Loan',
            'loanable_amount' => '50000',
            'fixed_deposit' => 2000,
            'comakers' => 0,
            'interest_rate' => 5.00, // base fallback rate
            'max_term_months' => 6,
            'available_terms' => [
                ['months' => 6, 'interest_rate' => 5.00],
                ['months' => 2, 'interest_rate' => 2.00],
                ['months' => 4, 'interest_rate' => 3.50],
            ],
            'is_active' => true,
        ]);

        $this->assertTrue($loan->hasCustomTerms());

        // Test sorting: should return 2, 4, 6
        $sorted = $loan->getSortedTerms();
        $this->assertCount(3, $sorted);
        $this->assertEquals(2, $sorted[0]['months']);
        $this->assertEquals(2.00, $sorted[0]['interest_rate']);
        $this->assertEquals(4, $sorted[1]['months']);
        $this->assertEquals(3.50, $sorted[1]['interest_rate']);
        $this->assertEquals(6, $sorted[2]['months']);
        $this->assertEquals(5.00, $sorted[2]['interest_rate']);

        // Test interest rate lookup per term
        $this->assertEquals(2.00, $loan->getInterestRateForTerm(2));
        $this->assertEquals(3.50, $loan->getInterestRateForTerm(4));
        $this->assertEquals(5.00, $loan->getInterestRateForTerm(6));
        $this->assertEquals(5.00, $loan->getInterestRateForTerm(12)); // fallback

        // Test allowed terms
        $this->assertTrue($loan->isTermAllowed(2));
        $this->assertTrue($loan->isTermAllowed(4));
        $this->assertTrue($loan->isTermAllowed(6));
        $this->assertFalse($loan->isTermAllowed(3));
        $this->assertFalse($loan->isTermAllowed(5));
    }

    public function test_legacy_loan_without_custom_terms_falls_back_gracefully(): void
    {
        $loan = Loan::create([
            'category' => 'regular',
            'type_key' => 'legacy_flat_loan',
            'name' => 'Legacy Flat Loan',
            'loanable_amount' => '30000',
            'fixed_deposit' => 1000,
            'comakers' => 0,
            'interest_rate' => 4.50,
            'max_term_months' => 12,
            'available_terms' => null,
            'is_active' => true,
        ]);

        $this->assertFalse($loan->hasCustomTerms());
        $this->assertEmpty($loan->getSortedTerms());
        $this->assertEquals(4.50, $loan->getInterestRateForTerm(6));
        $this->assertTrue($loan->isTermAllowed(6));
        $this->assertTrue($loan->isTermAllowed(12));
        $this->assertFalse($loan->isTermAllowed(18));
    }
}
