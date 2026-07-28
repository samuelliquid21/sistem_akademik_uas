<?php
require_once 'config.php';
require_once __DIR__ . '/../engine/inference.php';
$session = validate_token();

$results = InferenceEngine::allTestCases();
json_response($results);