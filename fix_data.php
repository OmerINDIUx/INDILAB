<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\Project::where('slug', 'infraestructura-biorresponsiva-y-un-nuevo-paradigma-para-el-entorno-construido')->first();
$c = $p->content;
// Inject image into first two slides of the carousel (index 5 based on dump)
if (isset($c['blocks'][5]['type']) && $c['blocks'][5]['type'] === 'carousel_adv') {
    $c['blocks'][5]['data']['slides'][0]['image'] = 'projects/blocks/TJ2WlcpLHHgFraiMyvZyIjsRr0YqZARg6WcovKQc.png';
    $c['blocks'][5]['data']['slides'][1]['image'] = 'projects/blocks/TJ2WlcpLHHgFraiMyvZyIjsRr0YqZARg6WcovKQc.png';
    $p->content = $c;
    $p->save();
    echo "SUCCESS: Data updated with images.\n";
} else {
    echo "ERROR: Block structure mismatch.\n";
    print_r($c['blocks']);
}
