<?php
declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

final class Biofiltro
{
    public static function taxaRemocao(float $antes, float $depois): float
    {
        if ($antes == 0.0) throw new InvalidArgumentException('Valor inicial zero: divisão por zero');
        if ($antes < 0 || $depois < 0) throw new InvalidArgumentException('Valores negativos inválidos');
        return ($antes - $depois) / $antes * 100;
    }

    public static function aplicar(float $concentracaoInicial, float $taxaPercentual): float
    {
        if ($taxaPercentual < 0 || $taxaPercentual > 100) throw new InvalidArgumentException('Taxa 0-100');
        if ($concentracaoInicial < 0) throw new InvalidArgumentException('Concentração inicial negativa');
        return $concentracaoInicial * (1 - $taxaPercentual / 100);
    }

    public static function multiCamadas(float $concentracaoInicial, array $taxas): float
    {
        foreach ($taxas as $taxa) $concentracaoInicial = self::aplicar($concentracaoInicial, (float) $taxa);
        return $concentracaoInicial;
    }

    public static function eficienciaGeral(array $antes, array $depois): array
    {
        $resultado = [];
        foreach ($antes as $chave => $valor) {
            if (!array_key_exists($chave, $depois)) throw new InvalidArgumentException("Sem par $chave em depois");
            $resultado[$chave] = self::taxaRemocao((float) $valor, (float) $depois[$chave]);
        }
        return $resultado;
    }

    public static function classificarEficiencia(float $taxa): string
    {
        if ($taxa < 0) return 'piora';
        if ($taxa < 30) return 'baixa';
        if ($taxa < 70) return 'moderada';
        return 'alta';
    }
}
