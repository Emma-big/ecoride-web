<?php
namespace Helpers;

function renderError(int $code): void {
    http_response_code($code);
    $view = BASE_PATH . "/src/views/error{$code}.php";
    if (file_exists($view)) {
        require_once $view;
    } else {
        // fallback minimal
        echo "<h1>Error $code</h1>";
    }
    exit;
}
