<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookLoanController;
use App\Http\Controllers\Api\CitizenController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\LetterRequestController;
use App\Http\Controllers\Api\LetterTypeController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProfileContentController;
use App\Http\Controllers\Api\ProfileSectionController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SignerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VillagePotentialController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

});

Route::middleware([
    'auth:sanctum',
    'permission:user-management',
])
    ->prefix('users')
    ->group(function () {

        Route::get('/', [UserController::class, 'index']);

        Route::post('/', [UserController::class, 'store']);

        Route::get('/{user:user_id}', [UserController::class, 'show']);

        Route::put('/{user:user_id}', [UserController::class, 'update']);

        Route::delete('/{user:user_id}', [UserController::class, 'destroy']);

    });

Route::middleware([
    'auth:sanctum',
    'permission:user-management',
])
    ->group(function () {

        Route::apiResource('roles', RoleController::class)
            ->parameters(['roles' => 'role_id']);
        Route::put('/roles/{role_id}/permissions', [RoleController::class, 'syncPermissions']);

        Route::apiResource('permissions', PermissionController::class)
            ->parameters(['permissions' => 'permission_id']);

        Route::get('/user-permissions', [UserPermissionController::class, 'index']);
        Route::post('/user-permissions', [UserPermissionController::class, 'store']);
        Route::delete('/user-permissions', [UserPermissionController::class, 'destroy']);

    });

Route::get('/village-potentials', [VillagePotentialController::class, 'index']);
Route::get('/village-potentials/{potential_id}', [VillagePotentialController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service_id}', [ServiceController::class, 'show']);
Route::get('/agendas', [AgendaController::class, 'index']);
Route::get('/agendas/{agenda_id}', [AgendaController::class, 'show']);
Route::get('/galleries', [GalleryController::class, 'index']);
Route::get('/galleries/{gallery_id}', [GalleryController::class, 'show']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{news_id}', [NewsController::class, 'show']);
Route::get('/news-categories', [NewsCategoryController::class, 'index']);
Route::get('/profile-sections', [ProfileSectionController::class, 'index']);
Route::get('/profile-contents', [ProfileContentController::class, 'index']);
Route::post('/feedbacks', [FeedbackController::class, 'store']);
Route::apiResource('regions', RegionController::class)->only(['index','store','update','destroy']);


Route::middleware('auth:sanctum')->group(function () {

    // Pengelolaan Surat
    Route::apiResource('letter-types', LetterTypeController::class)
        ->parameters(['letter-types' => 'letterType_id']);
    Route::get('letter-types/{letterType_id}/documents', [LetterTypeController::class, 'documents']);
    Route::post('letter-types/{letterType_id}/documents', [LetterTypeController::class, 'storeDocument']);
    Route::put('letter-type-documents/{letterTypeDocument_id}', [LetterTypeController::class, 'updateDocument']);
    Route::delete('letter-type-documents/{letterTypeDocument_id}', [LetterTypeController::class, 'destroyDocument']);

    Route::apiResource('letter-requests', LetterRequestController::class)
        ->parameters(['letter-requests' => 'letterRequest_id']);
    Route::post('letter-requests/{letterRequest_id}/verify', [LetterRequestController::class, 'verify']);
    Route::post('letter-requests/{letterRequest_id}/authorize', [LetterRequestController::class, 'authorize']);
    Route::get('letter-requests/{letterRequest_id}/status-histories', [LetterRequestController::class, 'statusHistories']);
    Route::post('letter-requests/{letterRequest_id}/attachments', [LetterRequestController::class, 'storeAttachment']);
    Route::get('letter-requests/{letterRequest_id}/attachments', [LetterRequestController::class, 'attachments']);

    // Pengaduan
    Route::apiResource('complaints', ComplaintController::class)
        ->parameters(['complaints' => 'complaint_id']);
    Route::post('complaints/{complaint_id}/status', [ComplaintController::class, 'updateStatus']);
    Route::get('complaints/{complaint_id}/status-histories', [ComplaintController::class, 'statusHistories']);
    Route::post('complaints/{complaint_id}/attachments', [ComplaintController::class, 'storeAttachment']);
    Route::get('complaints/{complaint_id}/attachments', [ComplaintController::class, 'attachments']);

    Route::apiResource('feedbacks', FeedbackController::class)
        ->parameters(['feedbacks' => 'feedback_id'])
        ->except(['store']);

    // Perpustakaan
    Route::apiResource('book-categories', BookCategoryController::class)
        ->parameters(['book-categories' => 'category_id']);
    Route::apiResource('books', BookController::class)
        ->parameters(['books' => 'book_id']);
    Route::apiResource('book-loans', BookLoanController::class)
        ->parameters(['book-loans' => 'loan_id']);

    // Manajemen akun citizen
    Route::apiResource('citizens', CitizenController::class)
        ->parameters(['citizens' => 'citizen_id']);

    //Signers
    Route::apiResource('signers', SignerController::class)
        ->parameters(['signers' => 'signer_id']);

   
    Route::middleware(RoleMiddleware::class . ':Superadmin,Admin,Operator')->group(function () {
        Route::apiResource('profile-sections', ProfileSectionController::class)
            ->parameters(['profile-sections' => 'section_id'])->except(['index']);
        Route::apiResource('profile-contents', ProfileContentController::class)
            ->parameters(['profile-contents' => 'profileContent_id'])->except(['index']);
        Route::apiResource('services', ServiceController::class)
            ->parameters(['services' => 'service_id'])->except(['index', 'show']);
        Route::apiResource('village-potentials', VillagePotentialController::class)
            ->parameters(['village-potentials' => 'potential_id'])->except(['index', 'show']);
        Route::apiResource('agendas', AgendaController::class)
            ->parameters(['agendas' => 'agenda_id'])->except(['index', 'show']);
        Route::apiResource('galleries', GalleryController::class)
            ->parameters(['galleries' => 'gallery_id'])->except(['index', 'show']);
        Route::apiResource('news-categories', NewsCategoryController::class)
            ->parameters(['news-categories' => 'category_id'])->except(['index']);
        Route::apiResource('news', NewsController::class)
            ->parameters(['news' => 'news_id'])->except(['index', 'show']);
    });

});

Route::middleware(['auth:sanctum', 'permission:dashboard'])
    ->get('/dashboard/summary', [DashboardController::class, 'summary']);

Route::middleware(['auth:sanctum', 'permission:dashboard'])
    ->get('/test-permission', function () {
        return response()->json([
            'success' => true,
            'message' => 'Anda memiliki permission Dashboard.',
        ]);
    });
