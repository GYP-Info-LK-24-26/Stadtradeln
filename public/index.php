<?php

/**
 * Main entry point for the application
 * 
 * All requests should be routed through this file.
 * Configure your web server to rewrite URLs to this file.
 */

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TeamController;
use App\Controllers\LeaderboardController;
use App\Controllers\SettingsController;

// Create router
$router = new Router();

// Define routes
$router
    // Home
    ->get('/', [HomeController::class, 'index'])
    
    // Auth
    ->get('/login', [AuthController::class, 'showLogin'])
    ->post('/login', [AuthController::class, 'login'])
    ->get('/register', [AuthController::class, 'showRegister'])
    ->post('/register', [AuthController::class, 'register'])
    ->get('/logout', [AuthController::class, 'logout'])
    
    // Dashboard
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->post('/dashboard/tour', [DashboardController::class, 'addTour'])
    ->post('/dashboard/tour/update', [DashboardController::class, 'updateTour'])
    ->post('/dashboard/tour/delete', [DashboardController::class, 'deleteTour'])
    
    // Team
    ->get('/team', [TeamController::class, 'index'])
    ->get('/team/join', [TeamController::class, 'showJoinCreate'])
    ->post('/team/join', [TeamController::class, 'joinOrCreate'])
    ->post('/team/leave', [TeamController::class, 'leave'])
    
    // Leaderboard
    ->get('/leaderboard', [LeaderboardController::class, 'index'])

    // Settings
    ->get('/settings', [SettingsController::class, 'index'])
    ->post('/settings', [SettingsController::class, 'updatePassword'])
    ->post('/settings/name', [SettingsController::class, 'updateName'])
    ->post('/settings/username', [SettingsController::class, 'updateUsername'])
    ->post('/settings/email', [SettingsController::class, 'updateEmail']);

// Resolve the current request
$router->resolve();
