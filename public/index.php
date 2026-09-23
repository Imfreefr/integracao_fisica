<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\ClassificadorQualidadeAgua;
use App\Biofiltro;

$obterValor = fn(string $chave): ?float => isset($_POST[$chave]) && $_POST[$chave] !== '' ? (float)$_POST[$chave] : null;
$resultado = null;
$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $antes = [
        'ph' => $obterValor('ph_antes'), 
        'turbidez' => $obterValor('turb_antes'), 
        'cloro' => $obterValor('cloro_antes'), 
        'dureza' => $obterValor('dur_antes'), 
        'temperatura' => $obterValor('temp_antes'), 
        'solidosTotais' => $obterValor('solidos_antes')];

        $depois = [
            'ph' => $obterValor('ph_depois'), 
            'turbidez' => $obterValor('turb_depois'), 
            'cloro' => $obterValor('cloro_depois'), 
            'dureza' => $obterValor('dur_depois'), 
            'temperatura' => $obterValor('temp_depois'), 
            'solidosTotais' => $obterValor('solidos_depois')];

        $avaliacaoAntes = ClassificadorQualidadeAgua::avaliarAmostra($antes);
        $avaliacaoDepois = ClassificadorQualidadeAgua::avaliarAmostra($depois);

        $eficiencia = Biofiltro::eficienciaGeral([
            'turbidez' => $antes['turbidez'], 
            'dureza' => $antes['dureza'], 
            'solidosTotais' => $antes['solidosTotais']], 
            ['turbidez' => $depois['turbidez'], 
            'dureza' => $depois['dureza'], 
            'solidosTotais' => $depois['solidosTotais']
        ]);
        $resultado = [
            'avaliacaoAntes' => $avaliacaoAntes, 
            'avaliacaoDepois' => $avaliacaoDepois, 
            'eficiencia' => $eficiencia];

        $caminhoArquivo = __DIR__ . '/../data/samples.json';
        $colecao = is_file($caminhoArquivo) ? json_decode((string)file_get_contents($caminhoArquivo), true) : [];
        if (!is_array($colecao)) $colecao = [];
        if (isset($colecao['antes'])) $colecao = [$colecao];
        $colecao[] = ['antes' => $antes, 
        'depois' => $depois, 
        'parecer_antes' => $avaliacaoAntes['parecer'], 
        'parecer_depois' => $avaliacaoDepois['parecer'], 
        'eficiencia' => $eficiencia, 'data' => date('c')];
        @mkdir(dirname($caminhoArquivo), 0777, true);
        file_put_contents($caminhoArquivo, json_encode($colecao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } catch (Throwable $excecao) {
        $erro = $excecao->getMessage();
    }
}
$valoresPadrao = [
    'ph_antes' => 7, 
    'turb_antes' => 8, 
    'cloro_antes' => 0.1, 
    'dur_antes' => 400, 
    'temp_antes' => 26, 
    'solidos_antes' => 650, 
    'ph_depois' => 7.2, 
    'turb_depois' => 1.5, 
    'cloro_depois' => 1.0, 
    'dur_depois' => 250, 
    'temp_depois' => 23, 
    'solidos_depois' => 320];
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Lab Agua - ODS 6</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="h4">Laboratorio Digital - Qualidade da Agua <small class="text-muted">ODS 6</small></h1>
        <p class="small text-muted">Portaria GM/MS 888/2021. pH 6-9.5 | Turbidez &le;5 uT | Cloro 0.2-2 mg/L | Dureza &le;500 mg/L | Solidos &le;500 ideal.</p><?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?><?php if ($resultado): ?><div class="row g-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Antes: <?= $resultado['avaliacaoAntes']['parecer'] ?></div>
                        <ul class="list-group list-group-flush"><?php foreach ($resultado['avaliacaoAntes']['parametros'] as $parametro): ?><li class="list-group-item d-flex justify-content-between"><span><?= $parametro['parametro'] ?> <?= $parametro['valor'] ?></span><span class="badge bg-<?= $parametro['situacao'] === 'potavel' ? 'success' : ($parametro['situacao'] === 'alerta' ? 'warning' : 'danger') ?>"><?= $parametro['rotulo'] ?></span></li><?php endforeach; ?></ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Depois: <?= $resultado['avaliacaoDepois']['parecer'] ?></div>
                        <ul class="list-group list-group-flush"><?php foreach ($resultado['avaliacaoDepois']['parametros'] as $parametro): ?><li class="list-group-item d-flex justify-content-between"><span><?= $parametro['parametro'] ?> <?= $parametro['valor'] ?></span><span class="badge bg-=" <?= $parametro['situacao'] === 'potavel' ? 'success' : ($parametro['situacao'] === 'alerta' ? 'warning' : 'danger') ?>"><?= $parametro['rotulo'] ?></span></li><?php endforeach; ?></ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Eficiencia Biofiltro</div>
                        <ul class="list-group list-group-flush"><?php foreach ($resultado['eficiencia'] as $chave => $valor): ?><li class="list-group-item d-flex justify-content-between"><span><?= $chave ?></span><span><?= number_format($valor, 1) ?>% (<?= Biofiltro::classificarEficiencia($valor) ?>)</span></li><?php endforeach; ?></ul>
                    </div>
                </div>
            </div>
            <hr><?php endif; ?><form method="post" class="card p-3">
            <div class="row">
                <div class="col-md-6">
                    <h6>Antes do filtro</h6><?php foreach ([['ph_antes', 'pH'], ['turb_antes', 'Turbidez (uT)'], ['cloro_antes', 'Cloro (mg/L)'], ['dur_antes', 'Dureza (mg/L)'], ['temp_antes', 'Temp (C)'], ['solidos_antes', 'Solidos Totais (mg/L)']] as [$chave, $rotulo]): ?><label class="form-label small"><?= $rotulo ?><input name="<?= $chave ?>" type="number" step="any" class="form-control form-control-sm" value="<?= htmlspecialchars((string)($_POST[$chave] ?? $valoresPadrao[$chave])) ?>" required></label><?php endforeach; ?>
                </div>
                <div class="col-md-6">
                    <h6>Depois do filtro</h6><?php foreach ([['ph_depois', 'pH'], ['turb_depois', 'Turbidez (uT)'], ['cloro_depois', 'Cloro (mg/L)'], ['dur_depois', 'Dureza (mg/L)'], ['temp_depois', 'Temp (C)'], ['solidos_depois', 'Solidos Totais (mg/L)']] as [$chave, $rotulo]): ?><label class="form-label small"><?= $rotulo ?><input name="<?= $chave ?>" type="number" step="any" class="form-control form-control-sm" value="<?= htmlspecialchars((string)($_POST[$chave] ?? $valoresPadrao[$chave])) ?>" required></label><?php endforeach; ?>
                </div>
            </div><button class="btn btn-primary mt-3">Calcular</button> <a href="data.php" target="_blank" class="btn btn-outline-secondary mt-3">Dataset</a>
        </form>
        <p class="small text-muted mt-3">Ref: Portaria GM/MS 888/2021; WHO GDWQ 4th ed.</p>
    </div>
</body>

</html>