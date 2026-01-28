<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::where('slug', 'infraestructura-biorresponsiva-y-un-nuevo-paradigma-para-el-entorno-construido')->first();
file_put_contents('project_content_dump.json', json_encode($project->content, JSON_PRETTY_PRINT));
echo "Dumped to project_content_dump.json\n";
