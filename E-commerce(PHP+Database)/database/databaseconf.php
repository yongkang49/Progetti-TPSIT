<?php
return [
    'dns' => 'mysql:host=localhost;dbname=forx_web',
    'username' => 'root',
    'pass' => '',
    'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES => false]
];