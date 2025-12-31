<?php
// Autoloader for Dompdf when not using Composer
spl_autoload_register(function ($class) {
    $prefix = 'Dompdf\\';
    $base_dir = __DIR__ . '/src/';
    $lib_dir  = __DIR__ . '/lib/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);

    // Check in src directory
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }

    // Check in lib directory for CPDF
    $file = $lib_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }
});
