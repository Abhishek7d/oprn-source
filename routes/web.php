<?php

use App\Http\Controllers\admin\ActionsController;
use App\Http\Controllers\admin\droplet\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebSiteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BlockStorageController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\admin\droplet\InvoiceController as Invoice;
use App\Http\Controllers\admin\droplet\ProjectController;
use App\Http\Controllers\admin\droplet\ServerController;
use App\Http\Controllers\admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin/password/reset/{token}', [AdminUserController::class, 'returnToFrontEnd'])->name('admin.password.reset');
Route::get('/admin/email/verification/{id}', [AdminUserController::class, 'verify'])->name('admin.verification.verify');

Route::group(['prefix' => 'api'], function () {

    // admin routes
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [AdminUserController::class, 'login']);
        Route::post('/resend', [AdminUserController::class, 'resend']);
        Route::get('/check', [AdminUserController::class, 'checkLogin']);
        //Route::get('email/verify/{id}', [AdminUserController::class, 'verify'])->name('admin.verification.verify');
        Route::post('/reset', [AdminUserController::class, 'reset']);
        Route::post('/reset/password', [AdminUserController::class, 'resetPassword']);
        Route::post('/logout', [AdminUserController::class, 'logout']);

        Route::group(['middleware' => 'checkAuth'], function () {

            // Actions
            Route::post('/actions/delete', [ActionsController::class, 'deleteSelected']);
            Route::post('/actions/set-value', [ActionsController::class, 'setValue']);
            Route::post('/actions/settings', [ActionsController::class, 'setSetting']);
            Route::get('/actions/settings/{option}/{json?}', [ActionsController::class, 'getSetting']);

            // Users
            Route::post('/change/password', [AdminUserController::class, 'changePassword']);
            Route::post('/users/create', [AdminUserController::class, 'register']);
            Route::post('/users', [AdminUserController::class, 'allUsers']);
            Route::get('/roles', [AdminUserController::class, 'getRoles']);
            Route::post('/change-access', [AdminUserController::class, 'changeAccess']);

            // Invoices
            Route::get('/invoice/generate/{date}', [Invoice::class, 'generateInvoice']);

            // Clients
            Route::post('/clients', [ClientController::class, 'clients']);

            // Projects
            Route::post('/projects', [ProjectController::class, 'allProjects']);

            // Droplets
            Route::post('/droplets', [ServerController::class, 'droplets']);
            Route::post('/applications', [ServerController::class, 'applications']);
            Route::post('/cron/{server}/{job}', [ServerController::class, 'setCronjob']);

            // Sizes
            Route::post('/sizes', [ServerController::class, 'sizes']);
            Route::post('/save-price', [ServerController::class, 'savePrice']);

            // Services
            Route::get('/droplets/services/{server}', [ServerController::class, 'getStatus']);
            Route::post('/droplets/services/{server}', [ServerController::class, 'setService']);
            Route::get('/droplets/resources/{server}', [ServerController::class, 'getResources']);
            Route::get('/droplets/cron-jobs/{server}', [ServerController::class, 'getCronJobs']);

            // Invoices
            Route::post('/invoices', [Invoice::class, 'invoices']);
        });
    });


    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/resend', [UserController::class, 'resend']);
    Route::get('/check', [UserController::class, 'checkLogin']);
    Route::get('email/verify/{id}', [UserController::class, 'verify'])->name('verification.verify');
    Route::post('/reset', [UserController::class, 'reset']);
    Route::post('/reset/password', [UserController::class, 'resetPassword']);
    Route::get('password/reset/{token}', [UserController::class, 'returnToFrontEnd'])->name('password.reset');
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('completed/{server_id}/{server_hash}', [DashboardController::class, 'serverCompleted']);
    Route::post('/access/validate-token', [DashboardController::class, 'validateInvitation']);

    Route::group(['middleware' => 'checkAuth'], function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/change/password', [UserController::class, 'changePassword']);

        //projects
        Route::post('/project/assign', [DashboardController::class, 'assignServers']);
        Route::post('/project', [DashboardController::class, 'createProject']);
        Route::post('/project/send-verification-code', [DashboardController::class, 'sendDeleteCode']);
        Route::post('/project/delete', [DashboardController::class, 'deleteProject']);
        Route::get('/projects/{all?}', [DashboardController::class, 'projects']);
        Route::post('/projects/delegate-access', [DashboardController::class, 'delegateAccess']);
        Route::get('/projects/delegate-access/{project}', [DashboardController::class, 'getDelegateAccess']);
        Route::post('/projects/delegate-access/status', [DashboardController::class, 'changeDuStatus']);
        Route::post('/projects/delegate-access/delete/{id}', [DashboardController::class, 'deleteDu']);

        Route::get('/access/delegate-accounts', [DashboardController::class, 'delegateAccounts']);
        Route::post('/access/accept-invitation', [DashboardController::class, 'acceptInvitation']);

        //droplets
        Route::get('/sizes', [DashboardController::class, 'availableSizes']);
        Route::post('/droplet/send-verification-code', [DashboardController::class, 'sendDeleteVerification']);
        Route::get('/regions', [DashboardController::class, 'availableRegions']);
        Route::post('/droplet', [DashboardController::class, 'createDroplet']);
        Route::post('/droplet/{id}', [DashboardController::class, 'dropletAction']);
        Route::get('/droplets', [DashboardController::class, 'droplets']);

        //application
        Route::get('/application/{all?}', [WebSiteController::class, 'showDomains']);
        Route::post('/application/new', [WebSiteController::class, 'addDomain']);
        Route::post('/application/{application}/update-domain', [WebSiteController::class, 'updateDomainName']);
        Route::post('/application/{application}/add-ssl', [WebSiteController::class, 'addSSLToDomain']);
        Route::post('/application/{application}/remove-ssl', [WebSiteController::class, 'removeSSLToDomain']);
        Route::post('/application/{application}/add-ftp', [WebSiteController::class, 'addFTPToApplication']);
        Route::post('/application/{application}/delete-ftp', [WebSiteController::class, 'removeFTPToApplication']);
        Route::post('/application/{application}/change-ftp-pass', [WebSiteController::class, 'changeFTPPassword']);
        Route::post('/application/{application}', [WebSiteController::class, 'removeDomain']);

        //services
        Route::get('/server/{server}', [ServiceController::class, 'getStatus']);
        Route::post('/server/{server}', [ServiceController::class, 'setService']);
        Route::get('/resources/{server}', [ServiceController::class, 'getResources']);

        //cronjob
        Route::get('/cron/{server}', [ServiceController::class, 'getCronjobs']);
        Route::post('/cron/{server}', [ServiceController::class, 'addCronjob']);
        Route::post('/cron/{server}/{job}', [ServiceController::class, 'setCronjob']);

        //block storage
        Route::post('/storage', [BlockStorageController::class, 'createBlockStorage']);
        Route::post('/storage/resize', [BlockStorageController::class, 'resizeBlockStorage']);
        Route::post('/storage/delete', [BlockStorageController::class, 'deleteBlockStorage']);

        // Notifications
        Route::get('notifications', [DashboardController::class, 'notification']);
        Route::get('notifications/check', [DashboardController::class, 'checkNotification']);
        Route::get('notifications/set-status', [DashboardController::class, 'changeNotificationStatus']);

        // Backups
        Route::post('/backup/create/{server}', [BackupController::class, 'createBackup']);
        Route::get('/backups/{server}/{application}', [BackupController::class, 'getBackups']);
        Route::get('/backups/{server}', [BackupController::class, 'getAllBackups']);
        Route::post('/backup/restore/{server}', [BackupController::class, 'restoreBackup']);

        // Invoices
        Route::post('/invoice/statistics', [InvoiceController::class, 'getStatistics']);
        Route::get('/invoice/{id}', [InvoiceController::class, 'getInvoiceDetails']);
        Route::post('/add-credit-card', [InvoiceController::class, 'addCreditCard']);
        Route::post('/credit-card-action', [InvoiceController::class, 'creditCardAction']);
        Route::get('/get-credit-card', [InvoiceController::class, 'getCreditCard']);
        Route::get('/get-transactions', [InvoiceController::class, 'getTransactions']);
    });
});
Route::view("/{path?}", "app");
Route::view("/{path?}/{subpath?}", "app");
Route::view("/{path?}/{subpath?}/{subsubpath?}", "app");

Route::any('/', [UserController::class, 'default']);
