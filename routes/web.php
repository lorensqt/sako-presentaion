<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Membership Form Download
Route::get('/downloads/membership-registration-form', function () {
    $filePath = public_path('forms/membership-registration-form-2026.pdf');
    if (!file_exists($filePath)) {
        abort(404, 'Registration form not found.');
    }
    return response()->download($filePath, 'ML-Sako-Membership-Registration-Form-2026.pdf', [
        'Content-Type' => 'application/pdf',
    ]);
})->name('download.membership-form');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    // PIN security routes
    Route::post('/pin/setup', [AuthController::class, 'setupPin'])->name('pin.setup');
    Route::post('/pin/verify', [AuthController::class, 'verifyPin'])->name('pin.verify');

    // OTP security routes
    Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Secure Signature Stream
    Route::get('/signatures/{user}', [AdminController::class, 'streamSignature'])->name('signature.show');

    // Collaborative Loan Approvals & Rejections (Accessible by member co-makers and admin staff approvers)
    Route::post('/loans/{application}/approve', [LoanApprovalController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{application}/reject', [LoanApprovalController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{application}/return', [LoanApprovalController::class, 'returnLoan'])->name('loans.return');
    Route::get('/loan-documents/{document}', [LoanApprovalController::class, 'viewDocument'])->name('loan.documents.show');
    Route::get('/loans/{application}/ledger', [LoanApprovalController::class, 'viewLedger'])->name('loans.ledger.show');
    Route::get('/loans/{application}/schedule', [LoanApprovalController::class, 'viewSchedule'])->name('loans.schedule.show');

    // Member Self-Service Portal (Isolated from internal staff admins)
    Route::middleware('member.portal')->group(function () {
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

        // Member Loan Applications
        Route::post('/loans/apply', [MemberController::class, 'applyLoan'])->name('member.loans.apply');
        Route::patch('/loans/{application}/replace-comaker', [MemberController::class, 'replaceCoMaker'])->name('member.loans.replace_comaker');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::delete('/users/{user}/signature', [AdminController::class, 'deleteSignature'])->name('admin.users.signature.destroy');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('admin.page:dashboard');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit-logs')->middleware('admin.page:audit_logs');
    
    // System Administrators Management (Super Admin Exclusive)
    Route::middleware('admin.super')->group(function () {
        Route::get('/administrators', [AdminController::class, 'administrators'])->name('admin.administrators');
        Route::post('/administrators', [AdminController::class, 'storeAdministrator'])->name('admin.administrators.store');
        Route::put('/administrators/{user}', [AdminController::class, 'updateAdministrator'])->name('admin.administrators.update');
        Route::post('/administrators/{user}/reset-credentials', [AdminController::class, 'resetAdminCredentials'])->name('admin.administrators.reset_credentials');
        Route::delete('/administrators/{user}', [AdminController::class, 'deleteAdministrator'])->name('admin.administrators.destroy');
    });
    
    // Members Directory & Actions
    Route::middleware('admin.page:members')->group(function () {
        Route::get('/members', [AdminController::class, 'members'])->name('admin.members');
        Route::post('/members', [AdminController::class, 'storeMember'])->name('admin.members.store');
        Route::put('/members/{user}', [AdminController::class, 'updateMember'])->name('admin.members.update');
        Route::post('/members/{user}/reset-credentials', [AdminController::class, 'resetMemberCredentials'])->name('admin.members.reset_credentials');
        Route::delete('/members/{user}', [AdminController::class, 'deleteMember'])->name('admin.members.destroy');
        Route::get('/members/{user}/pdf', [AdminController::class, 'exportMemberPdf'])->name('admin.members.pdf');
    });

    // Loans Directory
    Route::middleware('admin.page:loans')->group(function () {
        Route::get('/loans', [AdminController::class, 'loans'])->name('admin.loans');
        Route::get('/loans/{application}/pdf', [AdminController::class, 'exportLoanPdf'])->name('admin.loans.pdf');
    });

    // Loan Approvals
    Route::middleware('admin.page:loan_approvals')->group(function () {
        Route::get('/loan-approvals', [AdminController::class, 'loanApprovals'])->name('admin.loans.approvals');
        Route::delete('/loans/{application}', [AdminController::class, 'destroyApplication'])->name('admin.loans.destroy_application');
    });

    // Loan Products Management
    Route::middleware('admin.page:loans_management')->group(function () {
        Route::get('/loans/management', [AdminController::class, 'loansManagement'])->name('admin.loans.management');
        Route::post('/loans/products', [AdminController::class, 'storeLoan'])->name('admin.loans.store');
        Route::put('/loans/products/{loan}', [AdminController::class, 'updateLoan'])->name('admin.loans.update');
        Route::delete('/loans/products/{loan}', [AdminController::class, 'deleteLoan'])->name('admin.loans.destroy');
    });

    // Treasury Withdrawals
    Route::middleware('admin.page:withdrawals')->group(function () {
        Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('admin.withdrawals');
        Route::get('/withdrawals/export-pdf', [AdminController::class, 'exportWithdrawalsPdf'])->name('admin.withdrawals.pdf');
        Route::post('/withdrawals/{withdrawal}/status', [AdminController::class, 'updateWithdrawalStatus'])->name('admin.withdrawals.status');
    });

    // Treasury Deduction Adjustments
    Route::middleware('admin.page:deductions')->group(function () {
        Route::get('/deductions', [AdminController::class, 'deductions'])->name('admin.deductions');
        Route::post('/deductions/{deductionRequest}/status', [AdminController::class, 'updateDeductionRequestStatus'])->name('admin.deductions.status');
        Route::get('/deductions/{deductionRequest}/pdf', [AdminController::class, 'exportDeductionPdf'])->name('admin.deductions.pdf');
    });
});
