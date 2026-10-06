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
    private const CAMPOS = [
        'ph_antes', 'turb_antes', 'cloro_antes', 'dur_antes', 'temp_antes', 'solidos_antes',
        'ph_depois', 'turb_depois', 'cloro_depois', 'dur_depois', 'temp_depois', 'solidos_depois',
    ];

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
        $valores = [];
        foreach (self::CAMPOS as $campo) {
            $valor = $entrada[$campo] ?? null;
            $valores[$campo] = is_numeric($valor) && trim((string) $valor) !== ''
                ? (float) $valor
                : null;
        }
        return $valores;
    }

    private function amostra(array $valores, string $momento): array
    {
        return ['ph' => $valores["ph_$momento"] ?? null, 'turbidez' => $valores["turb_$momento"] ?? null, 'cloro' => $valores["cloro_$momento"] ?? null, 'dureza' => $valores["dur_$momento"] ?? null, 'temperatura' => $valores["temp_$momento"] ?? null, 'solidosTotais' => $valores["solidos_$momento"] ?? null];
    }
}
