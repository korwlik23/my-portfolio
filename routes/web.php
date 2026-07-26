<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\Admin\AuditLogController;
use App\Http\Controllers\Web\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Web\Admin\TranslationController;
use App\Http\Controllers\Web\AdminRoleController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\LegalController;
use App\Http\Controllers\Web\PortfolioController;
use App\Http\Controllers\Web\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/robots.txt', [PortfolioController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');
Route::get('/resume/{locale}', [PortfolioController::class, 'downloadResume'])->name('resume.download');
Route::get('/media/{path}', [PortfolioController::class, 'media'])
    ->where('path', '.*')
    ->name('portfolio.media');

Route::get('/', [PortfolioController::class, 'index'])->name('landing');

Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('legal.terms');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('legal.refund');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/profile', [SettingController::class, 'index'])->name('profile');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/system', [SettingController::class, 'updateSystemSettings'])
        ->middleware('permission:settings.manage')
        ->name('settings.system');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('permission:admin.access')
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

            Route::prefix('portfolio')
                ->name('portfolio.')
                ->middleware('permission:portfolio.view')
                ->group(function () {
                    Route::get('/', [AdminPortfolioController::class, 'dashboard'])->name('dashboard');
                    Route::get('/settings', [AdminPortfolioController::class, 'settings'])
                        ->middleware('permission:portfolio.manage')
                        ->name('settings');
                    Route::put('/settings', [AdminPortfolioController::class, 'updateSettings'])
                        ->middleware('permission:portfolio.manage')
                        ->name('settings.update');
                    Route::get('/content/{block}', [AdminPortfolioController::class, 'editBlock'])
                        ->middleware('permission:portfolio.manage')
                        ->name('content.edit');
                    Route::put('/content/{block}', [AdminPortfolioController::class, 'updateBlock'])
                        ->middleware('permission:portfolio.manage')
                        ->name('content.update');

                    Route::middleware('permission:seo.manage')->group(function () {
                        Route::get('/seo', [AdminPortfolioController::class, 'seoIndex'])->name('seo.index');
                        Route::get('/seo/create', [AdminPortfolioController::class, 'seoCreate'])->name('seo.create');
                        Route::post('/seo', [AdminPortfolioController::class, 'seoStore'])->name('seo.store');
                        Route::get('/seo/{seo}/edit', [AdminPortfolioController::class, 'seoEdit'])->name('seo.edit');
                        Route::put('/seo/{seo}', [AdminPortfolioController::class, 'seoUpdate'])->name('seo.update');
                        Route::delete('/seo/{seo}', [AdminPortfolioController::class, 'seoDestroy'])->name('seo.destroy');
                    });

                    Route::middleware('permission:portfolio.manage')->group(function () {
                        Route::get('/{resource}', [AdminPortfolioController::class, 'index'])->name('resources.index');
                        Route::get('/{resource}/create', [AdminPortfolioController::class, 'create'])->name('resources.create');
                        Route::post('/{resource}', [AdminPortfolioController::class, 'store'])->name('resources.store');
                        Route::get('/{resource}/{id}/edit', [AdminPortfolioController::class, 'edit'])->name('resources.edit');
                        Route::put('/{resource}/{id}', [AdminPortfolioController::class, 'update'])->name('resources.update');
                        Route::delete('/{resource}/{id}', [AdminPortfolioController::class, 'destroy'])->name('resources.destroy');
                    });
                });

            Route::middleware('permission:user.manage')->group(function () {
                Route::get('/users', [AdminController::class, 'users'])->name('users');
                Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
                Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
                Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
                Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
                Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
                Route::put('/users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
                Route::put('/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
                Route::put('/users/{user}/reset-password', [AdminController::class, 'resetUserPassword'])
                    ->name('users.reset-password');
            });

            Route::resource('roles', AdminRoleController::class)
                ->except(['show'])
                ->middleware('permission:role.manage');

            Route::resource('translations', TranslationController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->middleware('permission:translation.manage');

            Route::get('/audit-logs', [AuditLogController::class, 'index'])
                ->middleware('permission:audit.view')
                ->name('audit-logs.index');
        });
});
