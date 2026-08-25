<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminElectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberElectionController;
use App\Http\Controllers\LoanApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/deploy-migrate-seed', function () {
    if (request('secret') !== 'sako-demo-2026') {
        return "Access denied.";
    }
    try {
        Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);
        return "Database migration and seeding successfully executed!<br><pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function() {
        return redirect()->route('member.savings');
    })->name('dashboard');
    Route::get('/savings', [MemberController::class, 'savings'])->name('member.savings');
    Route::get('/myloans', [MemberController::class, 'loans'])->name('member.loans');
    Route::get('/comaker-requests', [MemberController::class, 'coMakerRequests'])->name('member.comaker_requests');
    Route::get('/withdrawals', [MemberController::class, 'withdrawals'])->name('member.withdrawals');
    Route::post('/withdrawals', [MemberController::class, 'storeWithdrawal'])->name('member.withdrawals.store');
    Route::post('/withdrawals/{withdrawal}/cancel', [MemberController::class, 'cancelWithdrawal'])->name('member.withdrawals.cancel');
    Route::get('/deductions', [MemberController::class, 'deductions'])->name('member.deductions');
    Route::post('/deductions', [MemberController::class, 'storeDeductionRequest'])->name('member.deductions.store');
    Route::get('/loans', [MemberController::class, 'forms'])->name('member.forms');
    Route::get('/settings', [MemberController::class, 'settings'])->name('member.settings');
    Route::post('/settings', [MemberController::class, 'updateSettings'])->name('member.settings.update');

    // Loan Approvals & Rejections (collaborative workflow)
    Route::post('/loans/{application}/approve', [LoanApprovalController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{application}/reject', [LoanApprovalController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{application}/return', [LoanApprovalController::class, 'returnLoan'])->name('loans.return');

    // Member Loan Applications
    Route::post('/loans/apply', [MemberController::class, 'applyLoan'])->name('member.loans.apply');
    Route::patch('/loans/{application}/replace-comaker', [MemberController::class, 'replaceCoMaker'])->name('member.loans.replace_comaker');

    // Member Election & Voting
    Route::get('/elections', [MemberElectionController::class, 'index'])->name('member.elections.index');
    Route::get('/elections/{election}', [MemberElectionController::class, 'show'])->name('member.elections.show');
    Route::post('/elections/{election}/vote', [MemberElectionController::class, 'store'])->name('member.elections.vote');
    Route::get('/elections/{election}/results', [MemberElectionController::class, 'results'])->name('member.elections.results');

    // PIN security routes
    Route::post('/pin/setup', [AuthController::class, 'setupPin'])->name('pin.setup');
    Route::post('/pin/verify', [AuthController::class, 'verifyPin'])->name('pin.verify');

    // OTP security routes
    Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit-logs');
    Route::get('/members', [AdminController::class, 'members'])->name('admin.members');
    Route::get('/loans', [AdminController::class, 'loans'])->name('admin.loans');
    Route::get('/loan-approvals', [AdminController::class, 'loanApprovals'])->name('admin.loans.approvals');
    Route::delete('/loans/{application}', [AdminController::class, 'destroyApplication'])->name('admin.loans.destroy_application');
    Route::get('/loans/management', [AdminController::class, 'loansManagement'])->name('admin.loans.management');
    Route::post('/loans/products', [AdminController::class, 'storeLoan'])->name('admin.loans.store');
    Route::put('/loans/products/{loan}', [AdminController::class, 'updateLoan'])->name('admin.loans.update');
    Route::delete('/loans/products/{loan}', [AdminController::class, 'deleteLoan'])->name('admin.loans.destroy');
    Route::get('/loans/{application}/pdf', [AdminController::class, 'exportLoanPdf'])->name('admin.loans.pdf');
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('admin.withdrawals');
    Route::get('/withdrawals/export-pdf', [AdminController::class, 'exportWithdrawalsPdf'])->name('admin.withdrawals.pdf');
    Route::post('/withdrawals/{withdrawal}/status', [AdminController::class, 'updateWithdrawalStatus'])->name('admin.withdrawals.status');

    // Admin Deductions Adjustments
    Route::get('/deductions', [AdminController::class, 'deductions'])->name('admin.deductions');
    Route::post('/deductions/{deductionRequest}/status', [AdminController::class, 'updateDeductionRequestStatus'])->name('admin.deductions.status');
    Route::get('/deductions/{deductionRequest}/pdf', [AdminController::class, 'exportDeductionPdf'])->name('admin.deductions.pdf');
    
    // Member CRUD Actions
    Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
    Route::put('/members/{user}', [AdminController::class, 'updateMember'])->name('admin.members.update');
    Route::delete('/members/{user}', [AdminController::class, 'deleteMember'])->name('admin.members.destroy');
    Route::get('/members/{user}/pdf', [AdminController::class, 'exportMemberPdf'])->name('admin.members.pdf');

    // Admin Election Management
    Route::get('/elections', [AdminElectionController::class, 'index'])->name('admin.elections.index');
    Route::get('/elections/create', [AdminElectionController::class, 'create'])->name('admin.elections.create');
    Route::post('/elections', [AdminElectionController::class, 'store'])->name('admin.elections.store');
    Route::get('/elections/{election}', [AdminElectionController::class, 'show'])->name('admin.elections.show');
    Route::get('/elections/{election}/edit', [AdminElectionController::class, 'edit'])->name('admin.elections.edit');
    Route::put('/elections/{election}', [AdminElectionController::class, 'update'])->name('admin.elections.update');
    Route::delete('/elections/{election}', [AdminElectionController::class, 'destroy'])->name('admin.elections.destroy');
    Route::get('/elections/{election}/results', [AdminElectionController::class, 'results'])->name('admin.elections.results');

    // Position & Candidate management
    Route::post('/elections/{election}/positions', [AdminElectionController::class, 'storePosition'])->name('admin.elections.positions.store');
    Route::delete('/positions/{position}', [AdminElectionController::class, 'destroyPosition'])->name('admin.positions.destroy');
    Route::post('/positions/{position}/candidates', [AdminElectionController::class, 'storeCandidate'])->name('admin.positions.candidates.store');
    Route::delete('/candidates/{candidate}', [AdminElectionController::class, 'destroyCandidate'])->name('admin.candidates.destroy');
});
