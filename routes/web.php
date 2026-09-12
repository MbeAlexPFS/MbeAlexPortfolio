<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', HomeController::class)->name('home');

Route::get('/projets', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projets/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projets/{project}/rendu', [ProjectController::class, 'preview'])->name('projects.preview');
Route::get('/projets/{project}/rendu/proxy', [ProjectController::class, 'proxyContent'])->name('projects.preview.proxy');

Route::get('/competences', [SkillController::class, 'index'])->name('skills.index');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin login
Route::middleware('guest')->group(function () {
    Route::get('/auth/connexion', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/auth/connexion', [AuthController::class, 'login'])->name('auth.login');
});

Route::post('/auth/deconnexion', [AuthController::class, 'logout'])->name('auth.logout');

// ChatBot
Route::post('/chatbot', [ChatBotController::class, 'chat'])->name('chatbot.chat');

// Maintenance login
Route::post('/maintenance/login', [AuthController::class, 'maintenanceLogin'])->name('maintenance.login');

// Admin
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/projets', [ProjectController::class, 'adminIndex'])->name('projects.index');
    Route::get('/projets/creer', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projets', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projets/{project}/editer', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projets/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projets/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projets/sync-github', [ProjectController::class, 'syncPreview'])->name('projects.sync-github');
    Route::post('/projets/sync-github/confirm', [ProjectController::class, 'confirmSync'])->name('projects.sync-github.confirm');
    Route::get('/projets/sync-github/progress/{progress}', [ProjectController::class, 'syncProgress'])->name('projects.sync-github.progress');
    Route::get('/projets/sync-github/progress/{progress}/status', [ProjectController::class, 'syncStatus'])->name('projects.sync-github.status');
    Route::post('/projets/{project}/miniature', [ProjectController::class, 'generateThumbnail'])->name('projects.thumbnail');
    Route::delete('/projets/{project}/miniature', [ProjectController::class, 'cancelThumbnail'])->name('projects.thumbnail.cancel');
    Route::get('/projets/{project}/miniature/status', [ProjectController::class, 'thumbnailStatus'])->name('projects.thumbnail.status');

    Route::get('/competences', [SkillController::class, 'adminIndex'])->name('skills.index');
    Route::post('/competences', [SkillController::class, 'store'])->name('skills.store');
    Route::put('/competences/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/competences/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');

    Route::get('/contact', [ContactController::class, 'adminIndex'])->name('contact.index');
    Route::get('/contact/{message}', [ContactController::class, 'showMessage'])->name('contact.show');
    Route::delete('/contact/{message}', [ContactController::class, 'destroy'])->name('contact.destroy');

    Route::get('/formations', [FormationController::class, 'adminIndex'])->name('formations.index');
    Route::get('/formations/creer', [FormationController::class, 'create'])->name('formations.create');
    Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');
    Route::get('/formations/{formation}/editer', [FormationController::class, 'edit'])->name('formations.edit');
    Route::put('/formations/{formation}', [FormationController::class, 'update'])->name('formations.update');
    Route::delete('/formations/{formation}', [FormationController::class, 'destroy'])->name('formations.destroy');

    Route::get('/profil-site', [AdminController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profil-site', [AdminController::class, 'updateProfile'])->name('profile.update');

    Route::post('/maintenance', [AdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');

    Route::get('/cv', [AdminController::class, 'cv'])->name('cv');
});
