<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/it-work-hub/non-app/activities/1/save',
        'POST',
        [],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json'],
        json_encode([
            'activities' => [
                [
                    'id' => null,
                    'type' => 'Aktivitas',
                    'sort_order' => 1,
                    'name' => 'Aktivitas A',
                    'start_date' => '2026-07-08',
                    'deadline' => '2026-07-09',
                    'adjustment_date' => null,
                    'notes' => 'Keterangan A',
                    'pics' => [4],
                    'status' => 'Not Started'
                ]
            ]
        ])
    )
);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
