<?php

// EXAMPLE FLOW : SAKO - HRMD - Credit Committee - ACCOUNTING - RELEASING
return [

    'travel' => [
        'travel_loan' => [
            'name' => 'Travel Loan',
            'partner' => 'CENTZ Travel & Tours',
            'loanable_amount' => 50000,
            'fixed_deposit' => 0,
            'service_charge' => false,
            'interest' => null,
            'comakers' => 0,
            'max_term_months' => 24,
            'minimum_membership_months' => null,
            'hrmd_approval' => false,
        ],
    ],

    'commodity' => [

        'appliance_gadget' => [
            'name' => 'Commodity Loan - Appliance & Gadget',
            'partner' => [
                'Abenson',
                'Aerophone',
                'Family Appliance',
            ],
            'loanable_amount' => 100000,
            'fixed_deposit' => 10000,
            'comakers' => 4,
            'max_term_months' => 24,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],

        'jewelry' => [
            'name' => 'Commodity Loan - Jewelry',
            'partner' => 'M Lhuillier Jewellers',
            'loanable_amount' => 100000,
            'fixed_deposit' => 10000,
            'comakers' => 4,
            'max_term_months' => 24,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],

        'eyeglasses' => [
            'name' => 'Commodity Loan - Eyeglasses',
            'partner' => 'S. Fernandez Optical Clinic',
            'loanable_amount' => 100000,
            'fixed_deposit' => 10000,
            'comakers' => 4,
            'max_term_months' => 24,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],

        'memorial_lot' => [
            'name' => 'Commodity Loan - Memorial Lot',
            'partner' => 'Cattleya Gardens & Memorial Park',
            'location' => 'Cordova, Cebu',
            'service_charge' => false,
            'interest' => false,
            'comakers' => 0,
            'max_term_years' => 5,
        ],

        'adtel_products' => [
            'name' => 'Commodity Loan - ADTEL Products',
            'partner' => 'ADTEL',
            'products' => [
                'Baron DTV Max',
                'Baron BRL Antenna',
                'Baron BXR Antenna',
                'Kent Gold+',
                'Kent Crystal',
                'Kent Alkaline Pitcher',
                'Kent Vegetable Cleaner',
                'Baron Smart TV',
            ],
        ],
    ],

    'regular' => [

        'instant' => [
            'name' => 'Instant Loan',
            'loanable_amount' => 5000,
            'fixed_deposit' => 2000,
            'comakers' => [
                '≤2000' => 0,
                '>2000' => 1,
            ],
            'max_term_months' => 5,
            'minimum_membership_months' => 3,
        ],

        'petty_cash' => [
            'name' => 'Petty Cash Loan',
            'loanable_amount' => 30000,
            'fixed_deposit' => 5000,
            'comakers' => [
                '≤10000' => 2,
                '>10000' => 4,
            ],
            'max_term_months' => 12,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],

        'maxi' => [
            'name' => 'Maxi Loan',
            'fixed_deposit' => 10000,
            'insurance' => true,
            'loan_retention' => '5% added to Share Capital if below ₱25,000',
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
            'comakers' => 4,

            'matrix' => [
                [
                    'years' => '<5',
                    'loanable_amount' => 50000,
                    'term_months' => 36,
                ],
                [
                    'years' => '5-9',
                    'loanable_amount' => 100000,
                    'term_months' => 48,
                ],
                [
                    'years' => '10-14',
                    'loanable_amount' => 200000,
                    'term_months' => 60,
                ],
                [
                    'years' => '15+',
                    'loanable_amount' => 400000,
                    'term_months' => 72,
                ],
            ],
        ],

        'preferential' => [
            'name' => 'Preferential Loan',
            'loanable_amount' => '80% of Share Capital',
            'requires_no_existing_loan' => true,
            'fixed_deposit' => 10000,
            'comakers' => 0,
            'max_term_months' => 48,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],
    ],

    'special' => [

        'birthday' => [
            'name' => 'Birthday Loan',
            'loanable_amount' => 5000,
            'fixed_deposit' => 2000,
            'comakers' => 1,
            'latest_payslip_required' => true,
            'max_term_months' => 5,
            'minimum_membership_months' => 3,
        ],

        'holiday' => [
            'name' => 'Holiday Loan',
            'loanable_amount' => 5000,
            'fixed_deposit' => 2000,
            'comakers' => 1,
            'latest_payslip_required' => true,
            'max_term_months' => 5,
            'minimum_membership_months' => 3,
            'applicable_during' => 'Regular Holidays',
        ],
    ],

    'seasonal' => [

        'lechon' => [
            'name' => 'Lechon Loan',
            'loanable_amount' => 10000,
            'fixed_deposit' => 2000,
            'comakers' => 1,
            'latest_payslip_required' => true,
            'max_term_months' => 5,
            'minimum_membership_months' => 3,
        ],
    ],

    'bonus_buyout' => [

        'all_bonus' => [
            'name' => 'A.L.L. Bonus Buy-Out',
            'loanable_amount' => '80% of Basic Salary',
            'fixed_deposit' => 5000,
            'comakers' => 4,
            'one_time_payment' => true,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],

        'thirteenth_month' => [
            'name' => '13th Month Bonus Buy-Out',
            'loanable_amount' => '80% of Basic Salary',
            'fixed_deposit' => 5000,
            'comakers' => 4,
            'one_time_payment' => true,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],
    ],

    'emergency' => [

        'emergency_loan' => [
            'name' => 'Emergency Loan',
            'covered_events' => [
                'Hospitalization',
                'Fire',
                'Natural Disaster',
            ],
            'loanable_amount' => 20000,
            'fixed_deposit' => 5000,
            'comakers' => 4,
            'max_term_months' => 24,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],
    ],

    'health' => [

        'sako_care' => [
            'name' => 'SAKO Care',
            'partner' => 'Life & Health HMP Inc.',
            'loanable_amount' => 'Depends on plan',
            'fixed_deposit' => 5000,
            'comakers' => 2,
            'term_months' => 12,
            'application_start' => 'October',
            'card_expiration' => 'December 17',
            'minimum_membership_months' => 3,
        ],
    ],

    'upcoming' => [

        'show_money' => [
            'name' => 'Show-Money Loan',
            'purpose' => 'Visa proof of funds / Bank Certificate',
            'requirements' => [
                'Regular Member',
                'Minimum ₱500 Savings Account',
            ],
        ],

        'leisure' => [
            'name' => 'Leisure Loan',
            'partner' => [
                'Cebu Safari & Adventure Park',
                'Mar El Resort',
                'Sogod, Cebu',
            ],
            'loanable_amount' => 100000,
            'fixed_deposit' => 10000,
            'comakers' => 4,
            'max_term_months' => 36,
            'minimum_membership_months' => 3,
            'hrmd_approval' => true,
        ],
    ],

];