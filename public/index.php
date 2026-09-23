<?php declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';
use App\ClassificadorQualidadeAgua; use App\Biofiltro;
$obterValor = fn(string $chave):?float => isset($_POST[$chave]) && $_POST[$chave]!=='' ? (float)$_POST[$chave] : null;
$resultado = null; $erro = null; $conforme = null; $reducaoTurbidez = null; $reducaoSolidos = null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        $antes=['ph'=>$obterValor('ph_antes'),'turbidez'=>$obterValor('turb_antes'),'cloro'=>$obterValor('cloro_antes'),'dureza'=>$obterValor('dur_antes'),'temperatura'=>$obterValor('temp_antes'),'solidosTotais'=>$obterValor('solidos_antes')];
        $depois=['ph'=>$obterValor('ph_depois'),'turbidez'=>$obterValor('turb_depois'),'cloro'=>$obterValor('cloro_depois'),'dureza'=>$obterValor('dur_depois'),'temperatura'=>$obterValor('temp_depois'),'solidosTotais'=>$obterValor('solidos_depois')];
        $avaliacaoAntes=ClassificadorQualidadeAgua::avaliarAmostra($antes);
        $avaliacaoDepois=ClassificadorQualidadeAgua::avaliarAmostra($depois);
        $eficiencia=Biofiltro::eficienciaGeral(['turbidez'=>$antes['turbidez'],'dureza'=>$antes['dureza'],'solidosTotais'=>$antes['solidosTotais']],['turbidez'=>$depois['turbidez'],'dureza'=>$depois['dureza'],'solidosTotais'=>$depois['solidosTotais']]);
        $resultado=['avaliacaoAntes'=>$avaliacaoAntes,'avaliacaoDepois'=>$avaliacaoDepois,'eficiencia'=>$eficiencia];
        $conforme = $avaliacaoDepois['parecer']==='POTAVEL';
        $reducaoTurbidez = $eficiencia['turbidez'];
        $reducaoSolidos = $eficiencia['solidosTotais'];
        $caminhoArquivo=__DIR__.'/../data/samples.json'; $colecao=is_file($caminhoArquivo)?json_decode((string)file_get_contents($caminhoArquivo),true):[]; if(!is_array($colecao))$colecao=[]; if(isset($colecao['antes']))$colecao=[$colecao]; $colecao[]=['antes'=>$antes,'depois'=>$depois,'parecer_antes'=>$avaliacaoAntes['parecer'],'parecer_depois'=>$avaliacaoDepois['parecer'],'eficiencia'=>$eficiencia,'data'=>date('c')]; @mkdir(dirname($caminhoArquivo),0777,true); file_put_contents($caminhoArquivo,json_encode($colecao,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    } catch(Throwable $excecao){ $erro=$excecao->getMessage(); }
}
$valoresPadrao=['ph_antes'=>7,'turb_antes'=>8,'cloro_antes'=>0.1,'dur_antes'=>400,'temp_antes'=>26,'solidos_antes'=>650,'ph_depois'=>7.2,'turb_depois'=>1.5,'cloro_depois'=>1.0,'dur_depois'=>250,'temp_depois'=>23,'solidos_depois'=>320];
function val(string $chave, array $padrao): string { return htmlspecialchars((string)($_POST[$chave] ?? $padrao[$chave]), ENT_QUOTES, 'UTF-8'); }
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Laboratório Digital — Qualidade da Água • ODS 6</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet"><style>
:root{--fundo:#f4f8f5;--verde:#12492c;--verde-hover:#0e3a23;--borda:#d8e5dc;--texto:#162118;--muted:#5a6b60;--radius:8px;--erro:#9a2a2a;--ok-bg:#e6f2ea;--atencao-bg:#fdf3d7}
*{box-sizing:border-box}html,body{margin:0;padding:0;background:var(--fundo);color:var(--texto);font-family:Manrope,system-ui,sans-serif;line-height:1.5}
a{color:inherit}
.topo{max-width:980px;margin:0 auto;padding:32px 24px 0}
.eyebrow{font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);font-weight:600}
.titulo{font-family:Sora,Manrope,sans-serif;font-weight:700;font-size:30px;margin:6px 0 8px;letter-spacing:-.02em;color:var(--texto)}
.subtitulo{margin:0;color:var(--muted);font-size:15px;max-width:640px}
.shell{max-width:980px;margin:24px auto;padding:0 24px 32px}
.blocos{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px}
@media(max-width:760px){.blocos{grid-template-columns:1fr}}
.bloco{background:#fff;border:1px solid var(--borda);border-radius:var(--radius);padding:18px 18px 14px}
.bloco legend{font-family:Sora,sans-serif;font-weight:600;font-size:13px;letter-spacing:.04em;text-transform:uppercase;color:var(--verde);padding:0 6px;margin-left:-6px}
.campo{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
.campo:last-child{margin-bottom:0}
.campo label{font-size:12px;font-weight:600;color:var(--texto);letter-spacing:.01em}
.campo input{width:100%;padding:10px 11px;border:1px solid var(--borda);border-radius:6px;background:#fbfdfb;font-family:Manrope,sans-serif;font-size:14px;color:var(--texto);outline:none}
.campo input:focus{border-color:var(--verde);box-shadow:0 0 0 3px rgba(18,73,44,.12)}
.campo input:invalid{border-color:#c9a6a6}
.resumo{margin-top:16px;border:1px solid var(--borda);border-radius:var(--radius);background:#fff;padding:14px 16px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between}
.resumo-texto{font-size:14px;font-weight:600}
.resumo-meta{font-size:13px;color:var(--muted);display:flex;gap:14px;flex-wrap:wrap}
.selo{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.03em;border:1px solid var(--borda)}
.selo-ok{background:var(--ok-bg);color:var(--verde);border-color:#b9d6c2}
.selo-atencao{background:var(--atencao-bg);color:#7a5a00;border-color:#f0d99a}
.erro{margin-top:16px;border:1px solid #e6c2c2;background:#fdf2f2;color:var(--erro);border-radius:var(--radius);padding:12px 14px;font-size:13px}
.acoes{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.btn{appearance:none;border-radius:6px;padding:11px 16px;font-family:Manrope,sans-serif;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;line-height:1;transition:.15s}
.btn-primario{background:var(--verde);color:#fff;border:1px solid var(--verde)}
.btn-primario:hover{background:var(--verde-hover)}
.btn-secundario{background:#fff;color:var(--verde);border:1px solid var(--verde)}
.btn-secundario:hover{background:#eef5ef}
.rodape{max-width:980px;margin:0 auto;padding:18px 24px 28px;border-top:1px solid var(--borda);color:var(--muted);font-size:12px;white-space:nowrap;overflow:auto}
@media(max-width:760px){.rodape{white-space:normal}}
</style></head><body>
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
<div class="campo"><label for="ph_antes">pH</label><input id="ph_antes" name="ph_antes" type="number" step="any" inputmode="decimal" required value="<?=val('ph_antes',$valoresPadrao)?>"></div>
<div class="campo"><label for="turb_antes">Turbidez (uT)</label><input id="turb_antes" name="turb_antes" type="number" step="any" inputmode="decimal" required value="<?=val('turb_antes',$valoresPadrao)?>"></div>
<div class="campo"><label for="cloro_antes">Cloro (mg/L)</label><input id="cloro_antes" name="cloro_antes" type="number" step="any" inputmode="decimal" required value="<?=val('cloro_antes',$valoresPadrao)?>"></div>
<div class="campo"><label for="dur_antes">Dureza (mg/L)</label><input id="dur_antes" name="dur_antes" type="number" step="any" inputmode="decimal" required value="<?=val('dur_antes',$valoresPadrao)?>"></div>
<div class="campo"><label for="temp_antes">Temperatura (°C)</label><input id="temp_antes" name="temp_antes" type="number" step="any" inputmode="decimal" required value="<?=val('temp_antes',$valoresPadrao)?>"></div>
<div class="campo"><label for="solidos_antes">TDS (mg/L)</label><input id="solidos_antes" name="solidos_antes" type="number" step="any" inputmode="decimal" required value="<?=val('solidos_antes',$valoresPadrao)?>"></div>
</fieldset>
<fieldset class="bloco">
<legend>Depois do filtro</legend>
<div class="campo"><label for="ph_depois">pH</label><input id="ph_depois" name="ph_depois" type="number" step="any" inputmode="decimal" required value="<?=val('ph_depois',$valoresPadrao)?>"></div>
<div class="campo"><label for="turb_depois">Turbidez (uT)</label><input id="turb_depois" name="turb_depois" type="number" step="any" inputmode="decimal" required value="<?=val('turb_depois',$valoresPadrao)?>"></div>
<div class="campo"><label for="cloro_depois">Cloro (mg/L)</label><input id="cloro_depois" name="cloro_depois" type="number" step="any" inputmode="decimal" required value="<?=val('cloro_depois',$valoresPadrao)?>"></div>
<div class="campo"><label for="dur_depois">Dureza (mg/L)</label><input id="dur_depois" name="dur_depois" type="number" step="any" inputmode="decimal" required value="<?=val('dur_depois',$valoresPadrao)?>"></div>
<div class="campo"><label for="temp_depois">Temperatura (°C)</label><input id="temp_depois" name="temp_depois" type="number" step="any" inputmode="decimal" required value="<?=val('temp_depois',$valoresPadrao)?>"></div>
<div class="campo"><label for="solidos_depois">TDS (mg/L)</label><input id="solidos_depois" name="solidos_depois" type="number" step="any" inputmode="decimal" required value="<?=val('solidos_depois',$valoresPadrao)?>"></div>
</fieldset>
</div>
<?php if($erro):?><div class="erro" role="alert">Valor inválido — revise os campos e tente novamente. <span style="opacity:.8">(<?=htmlspecialchars($erro,ENT_QUOTES,'UTF-8')?>)</span></div><?php endif;?>
<?php if($resultado!==null && $erro===null):?>
<section class="resumo" aria-live="polite">
<div class="resumo-texto"><?= $conforme ? 'Água filtrada dentro dos parâmetros' : 'Água filtrada fora dos parâmetros' ?> <span class="selo <?= $conforme ? 'selo-ok' : 'selo-atencao' ?>"><?= $conforme ? 'Conforme' : 'Requer atenção' ?></span></div>
<div class="resumo-meta"><span>Turbidez: <?= number_format((float)$reducaoTurbidez,1,',','.') ?>% de redução</span><span>TDS: <?= number_format((float)$reducaoSolidos,1,',','.') ?>% de redução</span></div>
</section>
<?php endif;?>
<div class="acoes">
<button class="btn btn-primario" type="submit">Calcular</button>
<button class="btn btn-secundario" type="button" id="botaoCsv" aria-label="Baixar dataset em CSV">
<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v13"/><path d="M7 11l5 5 5-5"/><path d="M3 17v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3"/></svg>
Baixar dataset
</button>
</div>
</form>
</main>
<footer class="rodape">Referências: Portaria GM/MS 888/2021 · pH 6–9,5 · Turbidez ≤ 5 uT · Cloro 0,2–2 mg/L · Dureza ≤ 500 mg/L · TDS ≤ 500 mg/L.</footer>
<script>
document.getElementById('botaoCsv').addEventListener('click',function(){
  const ids=[['ph_antes','ph_depois','pH'],['turb_antes','turb_depois','Turbidez (uT)'],['cloro_antes','cloro_depois','Cloro (mg/L)'],['dur_antes','dur_depois','Dureza (mg/L)'],['temp_antes','temp_depois','Temperatura (°C)'],['solidos_antes','solidos_depois','TDS (mg/L)']];
  const linhas=[['parametro','antes','depois']];
  ids.forEach(([a,b,rotulo])=>{
    const va=document.getElementById(a)?.value ?? '';
    const vb=document.getElementById(b)?.value ?? '';
    linhas.push([rotulo,va,vb]);
  });
  const csv=linhas.map(r=>r.map(v=>'"'+String(v).replaceAll('"','""')+'"').join(',')).join('\r\n');
  const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
  const url=URL.createObjectURL(blob);
  const link=document.createElement('a');
  link.href=url; link.download='dataset-agua-ods6.csv'; document.body.appendChild(link); link.click();
  setTimeout(()=>{URL.revokeObjectURL(url); link.remove();},500);
});
</script>
</body></html>
