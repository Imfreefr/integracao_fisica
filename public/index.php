<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\ClassificadorQualidadeAgua;
use App\Biofiltro;

$obterValor = fn(string $chave): ?float => isset($_POST[$chave]) && $_POST[$chave] !== '' ? (float)$_POST[$chave] : null;
$resultado = null;
$erro = null;
$conforme = null;
$reducaoTurbidez = null;
$reducaoSolidos = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $antes = ['ph' => $obterValor('ph_antes'), 'turbidez' => $obterValor('turb_antes'), 'cloro' => $obterValor('cloro_antes'), 'dureza' => $obterValor('dur_antes'), 'temperatura' => $obterValor('temp_antes'), 'solidosTotais' => $obterValor('solidos_antes')];
        $depois = ['ph' => $obterValor('ph_depois'), 'turbidez' => $obterValor('turb_depois'), 'cloro' => $obterValor('cloro_depois'), 'dureza' => $obterValor('dur_depois'), 'temperatura' => $obterValor('temp_depois'), 'solidosTotais' => $obterValor('solidos_depois')];
        $avaliacaoAntes = ClassificadorQualidadeAgua::avaliarAmostra($antes);
        $avaliacaoDepois = ClassificadorQualidadeAgua::avaliarAmostra($depois);
        $eficiencia = Biofiltro::eficienciaGeral(['turbidez' => $antes['turbidez'], 'dureza' => $antes['dureza'], 'solidosTotais' => $antes['solidosTotais']], ['turbidez' => $depois['turbidez'], 'dureza' => $depois['dureza'], 'solidosTotais' => $depois['solidosTotais']]);
        $resultado = ['avaliacaoAntes' => $avaliacaoAntes, 'avaliacaoDepois' => $avaliacaoDepois, 'eficiencia' => $eficiencia];
        $conforme = $avaliacaoDepois['parecer'] === 'POTAVEL';
        $reducaoTurbidez = $eficiencia['turbidez'];
        $reducaoSolidos = $eficiencia['solidosTotais'];
        $caminhoArquivo = __DIR__ . '/../data/samples.json';
        $colecao = is_file($caminhoArquivo) ? json_decode((string)file_get_contents($caminhoArquivo), true) : [];
        if (!is_array($colecao)) $colecao = [];
        if (isset($colecao['antes'])) $colecao = [$colecao];
        $colecao[] = ['antes' => $antes, 'depois' => $depois, 'parecer_antes' => $avaliacaoAntes['parecer'], 'parecer_depois' => $avaliacaoDepois['parecer'], 'eficiencia' => $eficiencia, 'data' => date('c')];
        @mkdir(dirname($caminhoArquivo), 0777, true);
        file_put_contents($caminhoArquivo, json_encode($colecao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } catch (Throwable $excecao) {
        $erro = $excecao->getMessage();
    }
}
$valoresPadrao = ['ph_antes' => 7, 'turb_antes' => 8, 'cloro_antes' => 0.1, 'dur_antes' => 400, 'temp_antes' => 26, 'solidos_antes' => 650, 'ph_depois' => 7.2, 'turb_depois' => 1.5, 'cloro_depois' => 1.0, 'dur_depois' => 250, 'temp_depois' => 23, 'solidos_depois' => 320];
function val(string $chave, array $padrao): string
{
    return htmlspecialchars((string)($_POST[$chave] ?? $padrao[$chave]), ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laboratório Digital — Qualidade da Água • ODS 6</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <header class="topo">
        <div class="eyebrow">ODS 6</div>
        <h1 class="titulo">Laboratório Digital</h1>
        <p class="subtitulo">Compare a qualidade da água antes e depois do filtro conforme a Portaria GM/MS 888/2021.</p>
    </header>
    <main class="shell">
        <form method="post" novalidate id="formulario">
            <div class="blocos">
                <fieldset class="bloco">
                    <legend>Antes do filtro</legend>
                    <div class="campo"><label for="ph_antes">pH</label><input id="ph_antes" name="ph_antes" type="number" step="any" inputmode="decimal" required value="<?= val('ph_antes', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="turb_antes">Turbidez (uT)</label><input id="turb_antes" name="turb_antes" type="number" step="any" inputmode="decimal" required value="<?= val('turb_antes', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="cloro_antes">Cloro (mg/L)</label><input id="cloro_antes" name="cloro_antes" type="number" step="any" inputmode="decimal" required value="<?= val('cloro_antes', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="dur_antes">Dureza (mg/L)</label><input id="dur_antes" name="dur_antes" type="number" step="any" inputmode="decimal" required value="<?= val('dur_antes', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="temp_antes">Temperatura (°C)</label><input id="temp_antes" name="temp_antes" type="number" step="any" inputmode="decimal" required value="<?= val('temp_antes', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="solidos_antes">TDS (mg/L)</label><input id="solidos_antes" name="solidos_antes" type="number" step="any" inputmode="decimal" required value="<?= val('solidos_antes', $valoresPadrao) ?>"></div>
                </fieldset>
                <fieldset class="bloco">
                    <legend>Depois do filtro</legend>
                    <div class="campo"><label for="ph_depois">pH</label><input id="ph_depois" name="ph_depois" type="number" step="any" inputmode="decimal" required value="<?= val('ph_depois', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="turb_depois">Turbidez (uT)</label><input id="turb_depois" name="turb_depois" type="number" step="any" inputmode="decimal" required value="<?= val('turb_depois', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="cloro_depois">Cloro (mg/L)</label><input id="cloro_depois" name="cloro_depois" type="number" step="any" inputmode="decimal" required value="<?= val('cloro_depois', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="dur_depois">Dureza (mg/L)</label><input id="dur_depois" name="dur_depois" type="number" step="any" inputmode="decimal" required value="<?= val('dur_depois', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="temp_depois">Temperatura (°C)</label><input id="temp_depois" name="temp_depois" type="number" step="any" inputmode="decimal" required value="<?= val('temp_depois', $valoresPadrao) ?>"></div>
                    <div class="campo"><label for="solidos_depois">TDS (mg/L)</label><input id="solidos_depois" name="solidos_depois" type="number" step="any" inputmode="decimal" required value="<?= val('solidos_depois', $valoresPadrao) ?>"></div>
                </fieldset>
            </div>
            <?php if ($erro): ?><div class="erro" role="alert">Valor inválido — revise os campos e tente novamente. <span style="opacity:.8">(<?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>)</span></div><?php endif; ?>
            <?php if ($resultado !== null && $erro === null): ?>
                <section class="resumo" aria-live="polite">
                    <div class="resumo-texto"><?= $conforme ? 'Água filtrada dentro dos parâmetros' : 'Água filtrada fora dos parâmetros' ?> <span class="selo <?= $conforme ? 'selo-ok' : 'selo-atencao' ?>"><?= $conforme ? 'Conforme' : 'Requer atenção' ?></span></div>
                    <div class="resumo-meta"><span>Turbidez: <?= number_format((float)$reducaoTurbidez, 1, ',', '.') ?>% de redução</span><span>TDS: <?= number_format((float)$reducaoSolidos, 1, ',', '.') ?>% de redução</span></div>
                </section>
            <?php endif; ?>
            <div class="acoes">
                <button class="btn btn-primario" type="submit">Calcular</button>
                <button class="btn btn-secundario" type="button" id="botaoCsv" aria-label="Baixar dataset em CSV">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3v13" />
                        <path d="M7 11l5 5 5-5" />
                        <path d="M3 17v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3" />
                    </svg>
                    Baixar dataset
                </button>
            </div>
        </form>
    </main>
    <footer class="rodape">Referências: Portaria GM/MS 888/2021 · pH 6–9,5 · Turbidez ≤ 5 uT · Cloro 0,2–2 mg/L · Dureza ≤ 500 mg/L · TDS ≤ 500 mg/L.</footer>
    <script src="js/app.js"></script>
</body>

</html>