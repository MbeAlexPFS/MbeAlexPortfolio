<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', HomeController::class)->name('home');

Route::get('/projets', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projets/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/competences', [SkillController::class, 'index'])->name('skills.index');

Route::get('/blog', [ArticleController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [ArticleController::class, 'show'])->name('blog.show');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/auth/inscription', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/auth/inscription', [AuthController::class, 'register'])->name('auth.register');

    Route::get('/auth/connexion', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/auth/connexion', [AuthController::class, 'login'])->name('auth.login');

    Route::get('/auth/verifier-otp', [AuthController::class, 'showVerifyOtp'])->name('auth.verify-otp');
    Route::post('/auth/verifier-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/auth/deconnexion', [AuthController::class, 'logout'])->name('auth.logout');

// Authenticated
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/sondages', [PollController::class, 'index'])->name('polls.index');
    Route::get('/sondages/{poll}', [PollController::class, 'show'])->name('polls.show');
    Route::post('/sondages/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');
    Route::get('/sondages/{poll}/resultats', [PollController::class, 'results'])->name('polls.results');

    Route::post('/articles/{article}/commentaires', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil/pseudo', [ProfileController::class, 'updatePseudo'])->name('profile.update-pseudo');
    Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/profil/newsletters', [ProfileController::class, 'updateNewsletters'])->name('profile.update-newsletters');
    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::delete('/profil/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
});

// Newsletter unsubscription (signed routes)
Route::get('/newsletters/desabonner/articles/{user}', [NewsletterController::class, 'unsubscribeArticles'])
    ->name('newsletter.unsubscribe-articles');
Route::get('/newsletters/desabonner/sondages/{user}', [NewsletterController::class, 'unsubscribePolls'])
    ->name('newsletter.unsubscribe-polls');

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/utilisateurs', [AdminController::class, 'users'])->name('users');
    Route::put('/utilisateurs/{user}/toggle-active', [AdminController::class, 'toggleUserActive'])->name('users.toggle-active');

    Route::get('/projets', [ProjectController::class, 'adminIndex'])->name('projects.index');
    Route::get('/projets/creer', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projets', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projets/{project}/editer', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projets/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projets/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('/competences', [SkillController::class, 'adminIndex'])->name('skills.index');
    Route::post('/competences', [SkillController::class, 'store'])->name('skills.store');
    Route::put('/competences/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/competences/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');

    Route::get('/blog', [ArticleController::class, 'adminIndex'])->name('blog.index');
    Route::get('/blog/creer', [ArticleController::class, 'create'])->name('blog.create');
    Route::post('/blog', [ArticleController::class, 'store'])->name('blog.store');
    Route::get('/blog/{article}/editer', [ArticleController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{article}', [ArticleController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{article}', [ArticleController::class, 'destroy'])->name('blog.destroy');

    Route::put('/commentaires/{comment}/approuver', [CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('/commentaires/{comment}/rejeter', [CommentController::class, 'reject'])->name('comments.reject');

    Route::get('/contact', [ContactController::class, 'adminIndex'])->name('contact.index');
    Route::get('/contact/{message}', [ContactController::class, 'showMessage'])->name('contact.show');
    Route::delete('/contact/{message}', [ContactController::class, 'destroy'])->name('contact.destroy');

    Route::get('/sondages', [PollController::class, 'adminIndex'])->name('polls.index');
    Route::get('/sondages/creer', [PollController::class, 'create'])->name('polls.create');
    Route::post('/sondages', [PollController::class, 'storePoll'])->name('polls.store');
    Route::get('/sondages/{poll}/editer', [PollController::class, 'edit'])->name('polls.edit');
    Route::put('/sondages/{poll}', [PollController::class, 'update'])->name('polls.update');
    Route::delete('/sondages/{poll}', [PollController::class, 'destroyPoll'])->name('polls.destroy');

    Route::post('/sondages/{poll}/questions', [PollController::class, 'storeQuestion'])->name('polls.questions.store');
    Route::delete('/questions/{question}', [PollController::class, 'destroyQuestion'])->name('polls.questions.destroy');

    Route::get('/profil-site', [AdminController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profil-site', [AdminController::class, 'updateProfile'])->name('profile.update');
});
