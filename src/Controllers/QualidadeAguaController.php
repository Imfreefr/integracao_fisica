<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\AmostraRepository;
use App\Models\Biofiltro;
use App\Models\ClassificadorQualidadeAgua;
use Throwable;

//controller da amostra de água 
final class QualidadeAguaController
{
    public function __construct(private readonly AmostraRepository $repositorio) {}

    public function formulario(array $entrada): array
    {
        $valores = $this->valoresFormulario($entrada);
        $view = ['valores' => $valores, 'resultado' => null, 'erro' => null];
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return $view;
        try {
            $antes = $this->amostra($valores, 'antes');
            $depois = $this->amostra($valores, 'depois');
            $avaliacaoAntes = ClassificadorQualidadeAgua::avaliarAmostra($antes);
            $avaliacaoDepois = ClassificadorQualidadeAgua::avaliarAmostra($depois);
            $eficiencia = Biofiltro::eficienciaGeral(
                ['turbidez' => $antes['turbidez'], 'dureza' => $antes['dureza'], 'solidosTotais' => $antes['solidosTotais']],
                ['turbidez' => $depois['turbidez'], 'dureza' => $depois['dureza'], 'solidosTotais' => $depois['solidosTotais']]
            );
            $this->repositorio->salvar(compact('antes', 'depois') + ['parecer_antes' => $avaliacaoAntes['parecer'], 'parecer_depois' => $avaliacaoDepois['parecer'], 'eficiencia' => $eficiencia, 'data' => date('c')]);
            $view['resultado'] = compact('avaliacaoAntes', 'avaliacaoDepois', 'eficiencia');
        } catch (Throwable $exception) {
            $view['erro'] = $exception->getMessage();
        }
        return $view;
    }

    private function valoresFormulario(array $entrada): array
    {
        $padroes = ['ph_antes' => 7, 'turb_antes' => 8, 'cloro_antes' => .1, 'dur_antes' => 400, 'temp_antes' => 26, 'solidos_antes' => 650, 'ph_depois' => 7.2, 'turb_depois' => 1.5, 'cloro_depois' => 1, 'dur_depois' => 250, 'temp_depois' => 23, 'solidos_depois' => 320];
        return array_replace($padroes, array_map(static fn($valor) => $valor === '' ? null : (float) $valor, $entrada));
    }

    private function amostra(array $valores, string $momento): array
    {
        return ['ph' => $valores["ph_$momento"] ?? null, 'turbidez' => $valores["turb_$momento"] ?? null, 'cloro' => $valores["cloro_$momento"] ?? null, 'dureza' => $valores["dur_$momento"] ?? null, 'temperatura' => $valores["temp_$momento"] ?? null, 'solidosTotais' => $valores["solidos_$momento"] ?? null];
    }
}
