<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\Acl\ModuleCategoryController;
use App\Http\Controllers\Admin\Acl\ModuleController;
use App\Http\Controllers\Admin\Acl\PortalUserController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\InterviewController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\VideoStreamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'xss'])->group(function () {

// Public: Frontend

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/members-portal', [FrontendController::class, 'showLogin'])->name('members-portal');
Route::post('/members-portal/request-otp', [FrontendController::class, 'requestOtp'])->middleware('throttle:3,3')->name('members-portal.request-otp');
Route::get('/members-portal/otp', [FrontendController::class, 'showOtp'])->name('members-portal.otp');
Route::post('/members-portal/verify-otp', [FrontendController::class, 'verifyOtp'])->middleware('throttle:3,3')->name('members-portal.verify-otp');
Route::middleware(['portal.auth'])->group(function () {

    Route::get('/members-portal/dashboard', [FrontendController::class, 'showPortal'])->name('portal');
    Route::get('/members-portal/interviews', [FrontendController::class, 'showInterviews'])->name('portal.interviews');
    Route::get('/members-portal/interviews/load-more', [FrontendController::class, 'loadMoreInterviews'])->name('portal.interviews.load-more');
    Route::get('/members-portal/interviews/{token}', [FrontendController::class, 'showInterview'])->name('portal.interview');
    Route::get('/members-portal/event-materials', [FrontendController::class, 'showEventMaterials'])->name('portal.materials');
    Route::get('/members-portal/event-materials/{token}', [FrontendController::class, 'showEventMaterial'])->name('portal.material');
    Route::get('/members-portal/agenda', [FrontendController::class, 'showAgenda'])->name('portal.agenda');
    Route::get('/members-portal/reports', [FrontendController::class, 'showReports'])->name('portal.reports');
    Route::get('/members-portal/reports/load-more', [FrontendController::class, 'loadMoreReports'])->name('portal.reports.load-more');
    Route::get('/members-portal/documentation', [FrontendController::class, 'showDocumentation'])->name('portal.documentation');
    Route::get('/members-portal/documentation/load-more', [FrontendController::class, 'loadMoreDocumentation'])->name('portal.documentation.load-more');
    Route::get('/members-portal/training', [FrontendController::class, 'showTraining'])->name('portal.training');
    Route::get('/members-portal/training/load-more', [FrontendController::class, 'loadMoreTraining'])->name('portal.training.load-more');
    Route::get('/members-portal/training/{token}', [FrontendController::class, 'showTrainingVideo'])->name('portal.training-video');
    Route::get('/members-portal/logout', [FrontendController::class, 'portalLogout'])->name('portal.logout');
    
    Route::get('/stream/video', VideoStreamController::class)->name('stream.video');

});

// Admin: Login (no auth)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminLoginController::class, 'index']);
    Route::get('/login', [AdminLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'doLogin'])->middleware('throttle:3,3')->name('login.do');
    Route::get('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Admin: Protected routes (ACL)
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Module Categories
        Route::get('/acl/module-categories', [ModuleCategoryController::class, 'index'])->name('acl.module-category.listing');
        Route::get('/acl/module-categories/show/{token}', [ModuleCategoryController::class, 'show'])->name('acl.module-category.show');
        Route::get('/acl/module-categories/edit/{token}', [ModuleCategoryController::class, 'edit'])->name('acl.module-category.edit');
        Route::post('/acl/module-categories/edit/{token}', [ModuleCategoryController::class, 'update']);
        Route::get('/acl/module-categories/add', [ModuleCategoryController::class, 'create'])->name('acl.module-category.add');
        Route::post('/acl/module-categories/add', [ModuleCategoryController::class, 'store']);
        Route::get('/acl/module-categories/search', [ModuleCategoryController::class, 'searchModuleCategory']);
        Route::post('/acl/module-categories/delete/{token}', [ModuleCategoryController::class, 'destroy'])->name('acl.module-category.delete');
        Route::get('/acl/module-categories/do-edit/{token}/{display_order}', [ModuleCategoryController::class, 'updateDisplayOrder']);

        // Modules
        Route::get('/acl/module', [ModuleController::class, 'index'])->name('acl.module.listing');
        Route::get('/acl/module/show/{token}', [ModuleController::class, 'show'])->name('acl.module.show');
        Route::get('/acl/module/edit/{token}', [ModuleController::class, 'edit'])->name('acl.module.edit');
        Route::post('/acl/module/edit/{token}', [ModuleController::class, 'update']);
        Route::get('/acl/module/add', [ModuleController::class, 'create'])->name('acl.module.add');
        Route::post('/acl/module/add', [ModuleController::class, 'store']);
        Route::get('/acl/module/search', [ModuleController::class, 'searchModule']);
        Route::post('/acl/module/delete/{token}', [ModuleController::class, 'destroy'])->name('acl.module.delete');
        Route::get('/acl/module/do-edit/{token}/{display_order}', [ModuleController::class, 'updateDisplayOrder']);

        // Portal Users (ACL)
        Route::get('/acl/portal-users', [PortalUserController::class, 'index'])->name('acl.portal-users.listing');
        Route::get('/acl/portal-users/add', [PortalUserController::class, 'create'])->name('acl.portal-users.add');
        Route::post('/acl/portal-users/add', [PortalUserController::class, 'store']);
        Route::get('/acl/portal-users/show/{token}', [PortalUserController::class, 'show'])->name('acl.portal-users.show');
        Route::get('/acl/portal-users/edit/{token}', [PortalUserController::class, 'edit'])->name('acl.portal-users.edit');
        Route::post('/acl/portal-users/edit/{token}', [PortalUserController::class, 'update']);
        Route::post('/acl/portal-users/toggle-status/{token}', [PortalUserController::class, 'toggleStatus'])->name('acl.portal-users.toggle-status');
        Route::post('/acl/portal-users/delete/{token}', [PortalUserController::class, 'destroy'])->name('acl.portal-users.delete');

        // Hero (ACL)
        Route::get('/acl/hero', [HeroController::class, 'index'])->name('acl.hero.listing');
        Route::get('/acl/hero/add', [HeroController::class, 'create'])->name('acl.hero.add');
        Route::post('/acl/hero/add', [HeroController::class, 'store']);
        Route::get('/acl/hero/edit/{token}', [HeroController::class, 'edit'])->name('acl.hero.edit');
        Route::post('/acl/hero/edit/{token}', [HeroController::class, 'update']);
        Route::post('/acl/hero/toggle-status/{token}', [HeroController::class, 'toggleStatus'])->name('acl.hero.toggle-status');
        Route::post('/acl/hero/delete/{token}', [HeroController::class, 'destroy'])->name('acl.hero.delete');

        // Interviews (ACL) – navbar item, same pattern as Hero
        Route::get('/acl/interviews', [InterviewController::class, 'index'])->name('acl.interviews.listing');
        Route::get('/acl/interviews/add', [InterviewController::class, 'create'])->name('acl.interviews.add');
        Route::post('/acl/interviews/add', [InterviewController::class, 'store']);
        Route::get('/acl/interviews/edit/{token}', [InterviewController::class, 'edit'])->name('acl.interviews.edit');
        Route::post('/acl/interviews/edit/{token}', [InterviewController::class, 'update']);
        Route::post('/acl/interviews/toggle-status/{token}', [InterviewController::class, 'toggleStatus'])->name('acl.interviews.toggle-status');
        Route::post('/acl/interviews/delete/{token}', [InterviewController::class, 'destroy'])->name('acl.interviews.delete');

        // Events (ACL) – navbar item
        Route::get('/acl/events', [EventController::class, 'index'])->name('acl.events.listing');
        Route::get('/acl/events/view/{token}', [EventController::class, 'show'])->name('acl.events.view');
        Route::post('/acl/events/{token}/participants', [EventController::class, 'storeParticipant'])->name('acl.events.participant.store');
        Route::post('/acl/events/participant/{id}', [EventController::class, 'updateParticipant'])->name('acl.events.participant.update');
        Route::delete('/acl/events/participant/{id}', [EventController::class, 'destroyParticipant'])->name('acl.events.participant.destroy');
        Route::post('/acl/events/participant/{epId}/documents', [EventController::class, 'storeParticipantDocument'])->name('acl.events.participant.document.store');
        Route::post('/acl/events/participant-document/{docId}', [EventController::class, 'updateParticipantDocument'])->name('acl.events.participant.document.update');
        Route::delete('/acl/events/participant-document/{docId}', [EventController::class, 'destroyParticipantDocument'])->name('acl.events.participant.document.destroy');
        Route::get('/acl/events/add', [EventController::class, 'create'])->name('acl.events.add');
        Route::post('/acl/events/add', [EventController::class, 'store']);
        Route::get('/acl/events/edit/{token}', [EventController::class, 'edit'])->name('acl.events.edit');
        Route::post('/acl/events/edit/{token}', [EventController::class, 'update']);
        Route::post('/acl/events/toggle-status/{token}', [EventController::class, 'toggleStatus'])->name('acl.events.toggle-status');
        Route::post('/acl/events/delete/{token}', [EventController::class, 'destroy'])->name('acl.events.delete');

        // Companies (ACL)
        Route::get('/acl/companies', [CompanyController::class, 'index'])->name('acl.companies.listing');
        Route::get('/acl/companies/add', [CompanyController::class, 'create'])->name('acl.companies.add');
        Route::post('/acl/companies/add', [CompanyController::class, 'store']);
        Route::get('/acl/companies/edit/{token}', [CompanyController::class, 'edit'])->name('acl.companies.edit');
        Route::post('/acl/companies/edit/{token}', [CompanyController::class, 'update']);
        Route::post('/acl/companies/delete/{token}', [CompanyController::class, 'destroy'])->name('acl.companies.delete');

        // Participants (ACL)
        Route::get('/acl/participants', [ParticipantController::class, 'index'])->name('acl.participants.listing');
        Route::get('/acl/participants/add', [ParticipantController::class, 'create'])->name('acl.participants.add');
        Route::post('/acl/participants/add', [ParticipantController::class, 'store']);
        Route::get('/acl/participants/edit/{token}', [ParticipantController::class, 'edit'])->name('acl.participants.edit');
        Route::post('/acl/participants/edit/{token}', [ParticipantController::class, 'update']);
        Route::post('/acl/participants/toggle-status/{token}', [ParticipantController::class, 'toggleStatus'])->name('acl.participants.toggle-status');
        Route::post('/acl/participants/delete/{token}', [ParticipantController::class, 'destroy'])->name('acl.participants.delete');

        // Documentation (ACL)
        Route::get('/acl/documentation', [DocumentationController::class, 'index'])->name('acl.documentation.listing');
        Route::get('/acl/documentation/add', [DocumentationController::class, 'create'])->name('acl.documentation.add');
        Route::post('/acl/documentation/add', [DocumentationController::class, 'store']);
        Route::get('/acl/documentation/edit/{token}', [DocumentationController::class, 'edit'])->name('acl.documentation.edit');
        Route::post('/acl/documentation/edit/{token}', [DocumentationController::class, 'update']);
        Route::post('/acl/documentation/toggle-status/{token}', [DocumentationController::class, 'toggleStatus'])->name('acl.documentation.toggle-status');
        Route::post('/acl/documentation/delete/{token}', [DocumentationController::class, 'destroy'])->name('acl.documentation.delete');

        // Training (ACL)
        Route::get('/acl/training', [TrainingController::class, 'index'])->name('acl.training.listing');
        Route::get('/acl/training/add', [TrainingController::class, 'create'])->name('acl.training.add');
        Route::post('/acl/training/add', [TrainingController::class, 'store']);
        Route::get('/acl/training/edit/{token}', [TrainingController::class, 'edit'])->name('acl.training.edit');
        Route::post('/acl/training/edit/{token}', [TrainingController::class, 'update']);
        Route::post('/acl/training/toggle-status/{token}', [TrainingController::class, 'toggleStatus'])->name('acl.training.toggle-status');
        Route::post('/acl/training/delete/{token}', [TrainingController::class, 'destroy'])->name('acl.training.delete');

        // Reports (ACL)
        Route::get('/acl/reports', [ReportController::class, 'index'])->name('acl.reports.listing');
        Route::get('/acl/reports/add', [ReportController::class, 'create'])->name('acl.reports.add');
        Route::post('/acl/reports/add', [ReportController::class, 'store']);
        Route::get('/acl/reports/edit/{token}', [ReportController::class, 'edit'])->name('acl.reports.edit');
        Route::post('/acl/reports/edit/{token}', [ReportController::class, 'update']);
        Route::post('/acl/reports/toggle-status/{token}', [ReportController::class, 'toggleStatus'])->name('acl.reports.toggle-status');
        Route::post('/acl/reports/delete/{token}', [ReportController::class, 'destroy'])->name('acl.reports.delete');
    });
});

});
