<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Joystick\AdminController;
use App\Http\Controllers\Joystick\PageController;
use App\Http\Controllers\Joystick\PostController;
use App\Http\Controllers\Joystick\SectionController;
use App\Http\Controllers\Joystick\AppController;

use App\Http\Controllers\Joystick\ModeController;
use App\Http\Controllers\Joystick\CompanyController;
use App\Http\Controllers\Joystick\BranchController;
use App\Http\Controllers\Joystick\RegionController;
use App\Http\Controllers\Joystick\UserController;
use App\Http\Controllers\Joystick\RoleController;
use App\Http\Controllers\Joystick\PermissionController;
use App\Http\Controllers\Joystick\LanguageController;

// Site Controllers
use App\Http\Controllers\InputController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController as BlogController;
use App\Http\Controllers\PageController as SiteController;

// Cargo Controllers
use App\Http\Controllers\Cargo\TrackController;
use App\Http\Controllers\Cargo\StatusController;
use App\Http\Controllers\Cargo\TrackExtensionController;

use App\Http\Livewire\Client\Index as ClientIndex;
use App\Http\Livewire\Client\Archive;

use App\Http\Livewire\Storage\Tracks;
use App\Http\Livewire\Storage\Reception;
use App\Http\Livewire\Storage\Sending;
use App\Http\Livewire\Storage\OnTheBorder;
use App\Http\Livewire\Storage\OnRoute;
use App\Http\Livewire\Storage\Sorting;
use App\Http\Livewire\Storage\SendLocally;
use App\Http\Livewire\Storage\Arrival;
use App\Http\Livewire\Storage\Giving;

Route::redirect('/', app()->getLocale());

// Client Livewire Routes
Route::redirect('client', '/'.app()->getLocale().'/client');
Route::group(['prefix' => '/{locale}/client', 'middleware' => ['auth']], function () {

    Route::get('/', ClientIndex::class);
    Route::get('tracks', ClientIndex::class);
    Route::get('archive', Archive::class);
});

// Storage Livewire Routes
Route::redirect('storage', '/'.app()->getLocale().'/storage');
Route::group(['prefix' => '/{locale}/storage', 'middleware' => ['auth', 'roles:admin|storekeeper-first|storekeeper-sorter|storekeeper-last']], function () {

    Route::get('tracks', Tracks::class);
    Route::get('/', Reception::class);
    Route::get('reception', Reception::class);
    Route::get('sending', Sending::class);
    Route::get('on-the-border', OnTheBorder::class);
    Route::get('on-route', OnRoute::class);
    Route::get('sorting', Sorting::class);
    Route::get('send-locally', SendLocally::class);
    Route::get('arrival', Arrival::class);
    Route::get('giving', Giving::class);
});

Route::group(['prefix' => '{locale}/admin', 'middleware' => ['auth', 'roles:admin|manager|partner']], function () {

    Route::get('/', [AdminController::class, 'index']);
    Route::get('filemanager', [AdminController::class, 'filemanager']);
    Route::get('frame-filemanager', [AdminController::class, 'frameFilemanager']);

    Route::resources([
        // Cargo
        'tracks' => TrackController::class,
        'statuses' => StatusController::class,

        // Content
        'pages' => PageController::class,
        'posts' => PostController::class,
        'sections' => SectionController::class,
        'modes' => ModeController::class,
        'apps' => AppController::class,

        // Resources
        'companies' => CompanyController::class,
        'branches' => BranchController::class,
        'regions' => RegionController::class,
        'users' => UserController::class,
        'roles' => RoleController::class,
        'permissions' => PermissionController::class,
        'languages' => LanguageController::class,
    ]);

    // Cargo
    Route::get('tracks/search/tracks', [TrackController::class, 'search']);
    Route::get('tracks/{id}/search/users', [TrackController::class, 'searchUsers']);
    Route::get('tracks/{id}/pin-user/{userId}', [TrackController::class, 'pinUser']);
    Route::get('tracks/{id}/unpin-user', [TrackController::class, 'unpinUser']);
    Route::get('tracks/user/{id}', [TrackController::class, 'tracksUser']);

    Route::get('reception-tracks', [TrackExtensionController::class, 'receptionTracks']);
    Route::get('arrival-tracks', [TrackExtensionController::class, 'arrivalTracks']);
    Route::post('upload-tracks', [TrackExtensionController::class, 'uploadTracks']);
    Route::post('export-tracks', [TrackExtensionController::class, 'exportTracks']);

    // Resources
    Route::get('companies-actions', [CompanyController::class, 'actionCompanies']);
    Route::get('users/search/user', [UserController::class, 'search']);
    // Route::get('users/search-ajax', [UserController::class, 'searchAjax']);
    Route::get('users/password/{id}/edit', [UserController::class, 'passwordEdit']);
    Route::put('users/password/{id}', [UserController::class, 'passwordUpdate']);
});

// Joystick Administration
Route::redirect('admin', '/'.app()->getLocale().'/admin');

// User Profile
Route::group(['prefix' => '{locale}', 'middleware' => 'auth'], function() {

    Route::get('profile', [ProfileController::class, 'profile']);
    Route::get('profile/edit', [ProfileController::class, 'editProfile']);
    Route::put('profile', [ProfileController::class, 'updateProfile']);
    Route::get('profile/password/edit', [ProfileController::class, 'passwordEdit']);
    Route::put('profile/password', [ProfileController::class, 'passwordUpdate']);
    Route::post('push-subscribe', [ProfileController::class, 'pushSubscribe']);
    Route::post('push-unsubscribe', [ProfileController::class, 'pushUnsubscribe']);
});

// Site
Route::group(['prefix' => '{locale}'], function() {

    // Unsubscribe for mail
    Route::get('unsubscribe/{token}/{id}', [InputController::class, 'unsubscribe']);
    Route::get('unsubscribe/done', [InputController::class, 'unsubscribeDone']);

    // Input Actions
    Route::get('search', [InputController::class, 'search']);
    Route::get('search-track', [InputController::class, 'searchTrack']);
    Route::get('search-ajax', [InputController::class, 'searchAjax']);
    Route::post('send-app', [InputController::class, 'sendApp']);
    Route::post('calculate', [InputController::class, 'calculate']);

    // News
    Route::get('i/news', [BlogController::class, 'posts']);
    Route::get('i/news/{page}', [BlogController::class, 'postSingle']);

    // Pages
    Route::get('i/contacts', [SiteController::class, 'contacts']);
    Route::get('i/{page}', [SiteController::class, 'page']);
    Route::get('/', [SiteController::class, 'index']);

});

require __DIR__.'/auth.php';