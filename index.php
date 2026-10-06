<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\QualidadeAguaController;
use App\Models\AmostraRepository;

$GLOBALS['appAssetPrefix'] = 'src/';
$dados = (new QualidadeAguaController(new AmostraRepository(__DIR__ . '/data/samples.json')))->formulario($_POST);
require __DIR__ . '/src/Views/agua.php';

//Index né chefe