<?php
// Boot Laravel and list roles with es_para_ccb flag
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = $app->make('db');
$roles = $db->table('roles')->select('nombre', 'es_para_ccb')->orderBy('nombre')->get();
foreach ($roles as $r) {
    $flag = isset($r->es_para_ccb) && $r->es_para_ccb ? '[CCB] ' : '      ';
    echo $flag . $r->nombre . PHP_EOL;
}
