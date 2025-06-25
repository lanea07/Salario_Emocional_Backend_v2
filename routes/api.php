<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\BenefitDetailController;
use App\Http\Controllers\BenefitUserController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PermissionController;

Route::middleware(['setLocale', 'validateApiVersion'])
    ->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::post('/register', 'register');
            Route::post('/login', 'login');
            Route::post('/validate-requirePassChange', 'validateRequirePassChange');
            Route::post('/validate-token', 'validateToken');
            Route::post('/validate-roles', 'validateAdmin');
        });

        Route::middleware(['jwt', 'hasActions'])->group(function () {

            Route::prefix('auth')->controller(AuthController::class)->group(function () {
                Route::get('/user', 'getUser')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/logout', 'logout')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/user', 'updateUser')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/passwordChange', 'passwordChange')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('login-as', 'loginAs')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('forgot-password', 'forgotPassword')->withoutMiddleware('auth:sanctum')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('benefit')->controller(BenefitController::class)->group(function () {
                Route::get('/', 'index')->name('benefit.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/available', 'indexAvailable')->name('benefit.indexavailable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/save', 'store')->name('benefit.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{benefit}', 'update')->name('benefit.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{benefit}', 'update')->name('benefit.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/benefit-settings', 'indexPreferences')->name('benefit-settings.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/benefit-settings/{benefit}', 'showPreferences')->name('benefit-settings.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/benefit-settings/{benefit}', 'storePreferences')->name('benefit-settings.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('benefit.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{benefit}', 'destroy')->name('benefit.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefit}', 'show')->name('benefit.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefit}/edit', 'edit')->name('benefit.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('benefit-detail')->controller(BenefitDetailController::class)->group(function () {
                Route::get('/', 'index')->name('benefitdetail.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/save', 'store')->name('benefitdetail.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{benefitdetail}', 'update')->name('benefitdetail.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{benefitdetail}', 'update')->name('benefitdetail.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('benefitdetail.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{benefitdetail}', 'destroy')->name('benefitdetail.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefitdetail}', 'show')->name('benefitdetail.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefitdetail}/edit', 'edit')->name('benefitdetail.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('benefit-user')->controller(BenefitUserController::class)->group(function () {
                Route::get('/', 'index')->name('benefituser.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/indexcollaboratorsnonapproved', 'indexCollaboratorsNonApproved')->name('benefituser.indexcollaboratorsnonapproved')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/indexnonapproved', 'indexNonApproved')->name('benefituser.indexnonapproved')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/indexcollaborators', 'indexCollaborators')->name('benefituser.indexCollaborators')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/benefituser', 'store')->name('benefituser.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{benefituser}', 'update')->name('benefituser.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{benefituser}', 'update')->name('benefituser.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/decidebenefituser', 'decideBenefitUser')->name('benefituser.decidebenefituser')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/exportbenefits', 'exportDetail')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{benefituser}', 'destroy')->name('benefituser.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefituser}/edit', 'edit')->name('benefituser.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{benefituser}', 'show')->name('benefituser.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{user}/{year}', 'showByUserID')->name('benefituser.showByUserID')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('position')->controller(PositionController::class)->group(function () {
                Route::get('/', 'index')->name('position.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/position', 'store')->name('position.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{position}', 'update')->name('position.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{position}', 'update')->name('position.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('position.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{position}', 'destroy')->name('position.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{position}', 'show')->name('position.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{position}/edit', 'edit')->name('position.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('role')->controller(RoleController::class)->group(function () {
                Route::get('/', 'index')->name('role.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/role', 'store')->name('role.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{role}', 'update')->name('role.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{role}', 'update')->name('role.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('role.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{role}', 'destroy')->name('role.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{role}', 'show')->name('role.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{role}/edit', 'edit')->name('role.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('permission')->controller(PermissionController::class)->group(function () {
                Route::get('/', 'index')->name('permission.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/permission', 'store')->name('permission.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{permission}', 'update')->name('permission.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{permission}', 'update')->name('permission.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('permission.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{permission}', 'destroy')->name('permission.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{permission}', 'show')->name('permission.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{permission}/edit', 'edit')->name('permission.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('user')->controller(UserController::class)->group(function () {
                Route::get('/', 'index')->name('user.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/user-descendants', 'indexDescendants')->name('user.indexDescendants')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/user', 'store')->name('user.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/update/{user}', 'update')->name('user.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/update/{user}', 'update')->name('user.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('user.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{user}', 'destroy')->name('user.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{user}', 'show')->name('user.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{user}/edit', 'edit')->name('user.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('dependency')->controller(DependencyController::class)->group(function () {
                Route::get('/', 'index')->name('dependency.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/dependencyAncestors/{id}', 'indexAncestors')->name('dependency.dependencyAncestors')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/getNonTreeValidDependencies', 'getNonTreeValidDependencies')->name('dependency.getNonTreeValidDependencies')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/dependency', 'store')->name('dependency.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/{dependency}', 'update')->name('dependency.updateWithPut')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::patch('/{dependency}', 'update')->name('dependency.updateWithPatch')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::post('/datatable', 'datatable')->name('dependency.datatable')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::delete('/{dependency}', 'destroy')->name('dependency.destroy')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{dependency}', 'show')->name('dependency.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{dependency}/edit', 'edit')->name('dependency.edit')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('admin')->controller(AdminController::class)->group(function () {
                Route::get('/getAllBenefitUser', 'getAllBenefitUser')->name('admin.getAllBenefitUser')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/getAllGroupedBenefits', 'getAllGroupedBenefits')->name('admin.getAllGroupedBenefits')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });

            Route::prefix('user-preferences')->controller(PreferencesController::class)->group(function () {
                Route::get('/', 'index')->name('preferences.index')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::get('/{user}', 'show')->name('preferences.show')->defaults('permissions', [1, 100])->defaults('endSession', true);
                Route::put('/{user}', 'store')->name('preferences.store')->defaults('permissions', [1, 100])->defaults('endSession', true);
            });
        });
    });
