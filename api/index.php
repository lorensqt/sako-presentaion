<?php

// When running on Vercel, copy the SQLite database to the writable /tmp directory if it doesn't exist
if (getenv('VERCEL') && !file_exists('/tmp/database.sqlite')) {
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb)) {
        copy($sourceDb, '/tmp/database.sqlite');
    } else {
        // Fallback: Create an empty database file if the template doesn't exist
        touch('/tmp/database.sqlite');
    }
}

// Forward Vercel requests to the public index.php
require __DIR__ . '/../public/index.php';
