<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $file = $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\View;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TeamController;
use App\Controllers\LeaderboardController;
use App\Controllers\SettingsController;

$router = new Router();

$router
    ->get('/', fn() => View::render('pages/home'))

    ->get('/login', [AuthController::class, 'showLogin'])
    ->post('/login', [AuthController::class, 'login'])
    ->get('/register', [AuthController::class, 'showRegister'])
    ->post('/register', [AuthController::class, 'register'])
    ->get('/logout', [AuthController::class, 'logout'])
    ->get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->get('/reset-password', [AuthController::class, 'showResetPassword'])
    ->post('/reset-password', [AuthController::class, 'resetPassword'])

    ->get('/dashboard', [DashboardController::class, 'index'])
    ->post('/dashboard/tour', [DashboardController::class, 'addTour'])
    ->post('/dashboard/tour/update', [DashboardController::class, 'updateTour'])
    ->post('/dashboard/tour/delete', [DashboardController::class, 'deleteTour'])

    ->get('/team', [TeamController::class, 'index'])
    ->get('/team/join', [TeamController::class, 'showJoinCreate'])
    ->post('/team/join', [TeamController::class, 'joinOrCreate'])
    ->post('/team/leave', [TeamController::class, 'leave'])
    ->post('/team/name', [TeamController::class, 'updateName'])

    ->get('/leaderboard', [LeaderboardController::class, 'index'])

    ->get('/settings', [SettingsController::class, 'index'])
    ->post('/settings', [SettingsController::class, 'updatePassword'])
    ->post('/settings/name', [SettingsController::class, 'updateName'])
    ->post('/settings/email', [SettingsController::class, 'updateEmail']);

$router->resolve();
