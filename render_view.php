<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::where('slug', 'infraestructura-biorresponsiva-y-un-nuevo-paradigma-para-el-entorno-construido')->first();
$view = view('projects.show', compact('project'))->render();
file_put_contents('debug_view.html', $view);
echo "View rendered to debug_view.html\n";
