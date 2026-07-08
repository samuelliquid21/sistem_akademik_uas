<?php
require_once 'config.php';
require_once __DIR__ . '/../engine/inference.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

$results = InferenceEngine::allTestCases();
json_response($results);
