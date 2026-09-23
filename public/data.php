<?php

declare(strict_types=1);
$caminhoArquivo = __DIR__ . '/../data/samples.json';
if (!is_file($caminhoArquivo)) {
    header('Content-Type: application/json; charset=utf-8');
    echo '[]';
    exit;
}
header('Content-Type: application/json; charset=utf-8');
readfile($caminhoArquivo);
