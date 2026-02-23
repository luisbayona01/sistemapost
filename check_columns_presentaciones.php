<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Presentacione;
use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('presentaciones');
echo "Columns in presentaciones: " . implode(', ', $columns) . "\n";
