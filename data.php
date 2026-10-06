<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(new App\Controllers\DadosController())->csv($_POST);

// Serve para enviar os dados cadastrados da aplicação viu professora ana luisa dev