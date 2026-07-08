<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';
$p = new App\Models\ItWhNonappProject();
var_dump(method_exists($p, 'activities'));
$p->activities();
echo "OK!\n";
