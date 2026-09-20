<?php

use App\Http\Controllers\Api\V1\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Api\V1\Admin\ApplicationFileController as AdminApplicationFileController;
use App\Http\Controllers\Api\V1\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Api\V1\Admin\OpinionFileController as AdminOpinionFileController;
use App\Http\Controllers\Api\V1\Admin\ProcedureController as AdminProcedureController;
use App\Http\Controllers\Api\V1\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Api\V1\Admin\ResultFileController as AdminResultFileController;
use App\Http\Controllers\Api\V1\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Api\V1\Admin\SupplementRequestController as AdminSupplementRequestController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Citizen\ApplicationController as CitizenApplicationController;
use App\Http\Controllers\Api\V1\Citizen\AppointmentController as CitizenAppointmentController;
use App\Http\Controllers\Api\V1\Citizen\NotificationController as CitizenNotificationController;
use App\Http\Controllers\Api\V1\Citizen\ProfileController as CitizenProfileController;
use App\Http\Controllers\Api\V1\Citizen\ResultFileController as CitizenResultFileController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Public\ApplicationTrackingController as PublicApplicationTrackingController;
use App\Http\Controllers\Api\V1\Public\ChatController as PublicChatController;
use App\Http\Controllers\Api\V1\Public\LocationController;
use App\Http\Controllers\Api\V1\Public\ProcedureController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('public')->group(function (): void {
        Route::get('procedures', [ProcedureController::class, 'index']);
        Route::get('procedures/{procedure}', [ProcedureController::class, 'show']);
        Route::get('procedures/{procedure}/ratings', [ProcedureController::class, 'ratings']);
        Route::get('provinces', [LocationController::class, 'provinces']);
        Route::get('provinces/{province}/wards', [LocationController::class, 'wards']);
        Route::get('fields', [LocationController::class, 'fields']);
        Route::get('statistics', [ProcedureController::class, 'statistics']);
        Route::post('application-tracking', [PublicApplicationTrackingController::class, 'store'])->middleware('throttle:public-application-tracking');
        Route::post('chat', [PublicChatController::class, 'sendMessage'])->middleware('throttle:chat');
    });

    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:auth-register');
        Route::post('register/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('register/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth-login');
        Route::post('login/verify-otp', [AuthController::class, 'verifyLoginOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('login/resend-otp', [AuthController::class, 'resendLoginOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('verify-login-otp', [AuthController::class, 'verifyLoginOtp'])->middleware('throttle:auth-verify-otp');
        Route::post('resend-login-otp', [AuthController::class, 'resendLoginOtp'])->middleware('throttle:auth-verify-otp');

        Route::post('refresh', [AuthController::class, 'refresh']);

        Route::middleware('auth:api')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
        });
    });

    Route::middleware('auth:api')->prefix('citizen')->group(function (): void {
        Route::get('profile', [CitizenProfileController::class, 'show']);
        Route::patch('profile', [CitizenProfileController::class, 'update']);
        Route::get('identity', [CitizenProfileController::class, 'identity']);
        Route::patch('identity', [CitizenProfileController::class, 'updateIdentity']);
        Route::post('password/otp', [CitizenProfileController::class, 'requestPasswordOtp'])->middleware('throttle:password-otp');
        Route::post('password/verify', [CitizenProfileController::class, 'verifyPasswordOtp'])->middleware('throttle:citizen-password-verify');
        Route::post('password/resend', [CitizenProfileController::class, 'resendPasswordOtp'])->middleware('throttle:password-otp');
        Route::get('applications', [CitizenApplicationController::class, 'index']);
        Route::post('applications', [CitizenApplicationController::class, 'store'])->middleware('throttle:citizen-submissions');
        Route::get('applications/{application}', [CitizenApplicationController::class, 'show']);
        Route::patch('applications/{application}', [CitizenApplicationController::class, 'update']);
        Route::post('applications/{application}/cancel', [CitizenApplicationController::class, 'cancel']);
        Route::post('applications/{application}/rating', [CitizenApplicationController::class, 'rate']);
        Route::post('applications/{application}/supplements', [\App\Http\Controllers\Api\V1\Citizen\ApplicationFileController::class, 'supplement']);
        Route::get('applications/{application}/result-files', [CitizenResultFileController::class, 'index'])->name('api.v1.citizen.application.result-files.index');
        Route::get('applications/{application}/result-files/{file}', [CitizenResultFileController::class, 'show'])->name('api.v1.citizen.application.result-files.show');
        Route::get('applications/{application}/files/{file}', [\App\Http\Controllers\Api\V1\Citizen\ApplicationFileController::class, 'show'])
            ->name('api.v1.citizen.application.files.show');
        Route::get('notifications', [CitizenNotificationController::class, 'index']);
        Route::get('notifications/{notification}', [CitizenNotificationController::class, 'show']);
        Route::post('notifications/{notification}/read', [CitizenNotificationController::class, 'markAsRead']);
    });

    Route::middleware('auth:api')->prefix('payments')->group(function (): void {
        Route::post('intents', [PaymentController::class, 'store']);
        Route::get('sync', [PaymentController::class, 'sync']);
        Route::get('intents/{intent}', [PaymentController::class, 'show']);
        Route::get('intents/{intent}/checkout', [PaymentController::class, 'checkout']);
    });
    Route::post('payments/webhooks/payos', [PaymentController::class, 'payosWebhook']);

    Route::middleware('auth:api')->prefix('citizen')->group(function (): void {
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payment-history', [PaymentController::class, 'history']);
        Route::get('payments/{intent}/invoice', [PaymentController::class, 'invoice'])
            ->name('api.v1.citizen.payments.invoice');
    });

    Route::middleware('auth:api')->prefix('citizen/appointments')->group(function (): void {
        Route::get('available-slots', [CitizenAppointmentController::class, 'slots']);
        Route::get('/', [CitizenAppointmentController::class, 'index']);
        Route::post('/', [CitizenAppointmentController::class, 'store']);
        Route::post('{appointment}/cancel', [CitizenAppointmentController::class, 'cancel']);
    });

    Route::middleware(['auth:api', 'appointment'])->prefix('admin')->group(function (): void {
        Route::get('dashboard', [AdminDashboardController::class, 'show']);
        Route::get('appointments', [AdminAppointmentController::class, 'index']);
        Route::post('appointments/check-in', [AdminAppointmentController::class, 'checkIn']);
        Route::post('appointments/reminders', [AdminAppointmentController::class, 'reminders']);
        Route::post('appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->middleware('staff');
        Route::post('appointments/{appointment}/reminder', [AdminAppointmentController::class, 'reminder']);
    });

    Route::middleware(['auth:api', 'staff'])->prefix('admin')->group(function (): void {
        Route::get('applications', [AdminApplicationController::class, 'index']);
        Route::get('applications/{application}', [AdminApplicationController::class, 'show']);
        Route::post('applications/{application}/accept', [AdminApplicationController::class, 'accept']);
        Route::post('applications/{application}/payments/counter', [AdminApplicationController::class, 'confirmCounterPayment']);
        Route::post('applications/{application}/complete-direct-reception', [AdminApplicationController::class, 'completeDirectReception']);
        Route::post('applications/{application}/reject', [AdminApplicationController::class, 'reject']);
        Route::post('applications/{application}/component-files', [AdminApplicationFileController::class, 'store'])->name('api.v1.admin.application.component-files.store');
        Route::get('applications/{application}/component-files/{file}', [AdminApplicationFileController::class, 'show'])->name('api.v1.admin.application.component-files.show');
        Route::post('applications/{application}/confirm-reception', [AdminApplicationController::class, 'confirmReception']);
        Route::post('applications/{application}/forward', [AdminApplicationController::class, 'forward']);
        Route::post('applications/{application}/approve', [AdminApplicationController::class, 'approve']);
        Route::post('applications/{application}/rework', [AdminApplicationController::class, 'rework']);
        Route::post('applications/{application}/deliver', [AdminApplicationController::class, 'deliver']);
        Route::patch('applications/{application}/general-info', [AdminApplicationController::class, 'updateGeneralInfo']);
        Route::post('applications/{application}/transitions', [AdminApplicationController::class, 'transition']);
        Route::post('applications/{application}/comments', [AdminApplicationController::class, 'comment']);
        Route::post('applications/{application}/opinion', [AdminApplicationController::class, 'opinion']);
        Route::get('applications/{application}/mail-history', [AdminApplicationController::class, 'mailHistory'])
            ->name('api.v1.admin.application.mail-history');
        Route::post('applications/{application}/mail', [AdminApplicationController::class, 'sendMail'])
            ->name('api.v1.admin.application.mail.send');
        Route::get('applications/{application}/events', [AdminApplicationController::class, 'events'])
            ->name('api.v1.admin.application.events');
        Route::post('applications/{application}/supplement-requests', [AdminSupplementRequestController::class, 'store']);
        Route::get('applications/{application}/result-files', [AdminResultFileController::class, 'index'])->name('api.v1.admin.application.result-files.index');
        Route::post('applications/{application}/result-files', [AdminResultFileController::class, 'store'])->name('api.v1.admin.application.result-files.store');
        Route::get('applications/{application}/result-files/{file}', [AdminResultFileController::class, 'show'])->name('api.v1.admin.application.result-files.show');
        Route::post('applications/{application}/result-files/{file}/sign', [AdminResultFileController::class, 'sign'])->name('api.v1.admin.application.result-files.sign');
        Route::delete('applications/{application}/result-files/{file}', [AdminResultFileController::class, 'destroy'])->name('api.v1.admin.application.result-files.destroy');
        Route::get('applications/{application}/opinion-files', [AdminOpinionFileController::class, 'index'])->name('api.v1.admin.application.opinion-files.index');
        Route::post('applications/{application}/opinion-files', [AdminOpinionFileController::class, 'store'])->name('api.v1.admin.application.opinion-files.store');
        Route::get('applications/{application}/opinion-files/{file}', [AdminOpinionFileController::class, 'show'])->name('api.v1.admin.application.opinion-files.show');
        Route::delete('applications/{application}/opinion-files/{file}', [AdminOpinionFileController::class, 'destroy'])->name('api.v1.admin.application.opinion-files.destroy');
        Route::get('fields', [AdminFieldController::class, 'index']);
        Route::get('procedures', [AdminProcedureController::class, 'index']);
        Route::get('procedures/{procedure}', [AdminProcedureController::class, 'show']);
        Route::get('reports/revenue', [AdminReportController::class, 'revenue']);
        Route::get('reports/applications', [AdminReportController::class, 'applications']);
        Route::middleware('administrator')->group(function (): void {
            Route::get('users', [AdminUserController::class, 'index']);
            Route::post('users', [AdminUserController::class, 'store']);
            Route::patch('users/{user}', [AdminUserController::class, 'update']);
            Route::delete('users/{user}', [AdminUserController::class, 'destroy']);
            Route::post('fields', [AdminFieldController::class, 'store']);
            Route::patch('fields/{field}', [AdminFieldController::class, 'update']);
            Route::delete('fields/{field}', [AdminFieldController::class, 'destroy']);
            Route::post('procedures', [AdminProcedureController::class, 'store']);
            Route::patch('procedures/{procedure}', [AdminProcedureController::class, 'update']);
            Route::delete('procedures/{procedure}', [AdminProcedureController::class, 'destroy']);
            Route::get('staff', [AdminStaffController::class, 'index']);
            Route::get('staff/counters', [AdminStaffController::class, 'counters']);
            Route::get('staff/{staff}', [AdminStaffController::class, 'show']);
            Route::post('staff', [AdminStaffController::class, 'store']);
            Route::patch('staff/{staff}', [AdminStaffController::class, 'update']);
            Route::delete('staff/{staff}', [AdminStaffController::class, 'destroy']);
        });
    });
});
