<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$path = __DIR__.'/resources/views/superadmin/gym.blade.php';
$compiled = app('blade.compiler')->compileString(file_get_contents($path));
file_put_contents(__DIR__.'/.tmp_compiled_superadmin_gym.php', $compiled);
echo "compiled\n";
