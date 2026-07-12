<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::first();
    if (!$user) {
        echo "ERROR: No users in database. Please run seeders.\n";
        exit(1);
    }
    auth()->login($user);
    echo "Logged in as: " . $user->email . "\n";
    $view = app(App\Http\Controllers\Admin\DashboardController::class)->index(request());
    echo "Dashboard index method executed successfully.\n";
    $rendered = $view->render();
    echo "Dashboard rendered successfully (length: " . strlen($rendered) . ").\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
