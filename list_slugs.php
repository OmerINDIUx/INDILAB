<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach(\App\Models\Project::all() as $p) {
    echo "ID: " . $p->id . " | Slug: " . $p->slug . PHP_EOL;
}
