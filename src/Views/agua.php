<?php
declare(strict_types=1);

function e(mixed $valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); }
function campo(string $id, string $rotulo, array $valores): void { echo '<div class="campo"><label for="' . e($id) . '">' . e($rotulo) . '</label><input id="' . e($id) . '" name="' . e($id) . '" type="number" step="any" inputmode="decimal" required value="' . e($valores[$id] ?? '') . '"></div>'; }

$valores = $dados['valores'];
$resultado = $dados['resultado'];
$erro = $dados['erro'];
$eficiencia = $resultado['eficiencia'] ?? [];
$conforme = ($resultado['avaliacaoDepois']['parecer'] ?? null) === 'POTAVEL';
$assetPrefix = $GLOBALS['appAssetPrefix'] ?? 'src/';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Laboratório Digital — Qualidade da Água • ODS 6</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= e($assetPrefix) ?>css/style.css">
</head>
<body>
    <header class="topo"><div class="eyebrow">ODS 6</div><h1 class="titulo">Laboratório Digital</h1><p class="subtitulo">Compare a qualidade da água antes e depois do filtro conforme a Portaria GM/MS 888/2021.</p></header>
    <main class="shell"><form method="post"><div class="blocos">
        <fieldset class="bloco"><legend>Antes do filtro</legend><?php campo('ph_antes', 'pH', $valores); campo('turb_antes', 'Turbidez (uT)', $valores); campo('cloro_antes', 'Cloro (mg/L)', $valores); campo('dur_antes', 'Dureza (mg/L)', $valores); campo('temp_antes', 'Temperatura (°C)', $valores); campo('solidos_antes', 'TDS (mg/L)', $valores); ?></fieldset>
        <fieldset class="bloco"><legend>Depois do filtro</legend><?php campo('ph_depois', 'pH', $valores); campo('turb_depois', 'Turbidez (uT)', $valores); campo('cloro_depois', 'Cloro (mg/L)', $valores); campo('dur_depois', 'Dureza (mg/L)', $valores); campo('temp_depois', 'Temperatura (°C)', $valores); campo('solidos_depois', 'TDS (mg/L)', $valores); ?></fieldset>
    </div>
    <?php if ($erro): ?><div class="erro" role="alert">Valor inválido — revise os campos e tente novamente. <span style="opacity:.8">(<?= e($erro) ?>)</span></div><?php endif; ?>
    <?php if ($resultado && !$erro): ?><section class="resumo" aria-live="polite"><div class="resumo-texto"><?= $conforme ? 'Água filtrada dentro dos parâmetros' : 'Água filtrada fora dos parâmetros' ?> <span class="selo <?= $conforme ? 'selo-ok' : 'selo-atencao' ?>"><?= $conforme ? 'Conforme' : 'Requer atenção' ?></span></div><div class="resumo-meta"><span>Turbidez: <?= number_format((float) $eficiencia['turbidez'], 1, ',', '.') ?>% de redução</span><span>TDS: <?= number_format((float) $eficiencia['solidosTotais'], 1, ',', '.') ?>% de redução</span></div></section><?php endif; ?>
    <div class="acoes"><button class="btn btn-primario" type="submit">Calcular</button><button class="btn btn-secundario" type="submit" formaction="data.php" formmethod="post">Baixar dataset</button></div>
    </form></main>
    <footer class="rodape">Referências: Portaria GM/MS 888/2021 · pH 6–9,5 · Turbidez ≤ 5 uT · Cloro 0,2–2 mg/L · Dureza ≤ 500 mg/L · TDS ≤ 500 mg/L.</footer>
</body>
</html>
