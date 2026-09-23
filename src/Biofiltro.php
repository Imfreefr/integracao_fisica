<?php
declare(strict_types=1);
namespace App;

use InvalidArgumentException;

final class Biofiltro
{
    public static function taxaRemocao(float $antes, float $depois): float {
        if ($antes == 0.0) throw new InvalidArgumentException('Valor inicial zero: divisão por zero');
        if ($antes < 0 || $depois < 0) throw new InvalidArgumentException('Valores negativos inválidos');
        return ($antes - $depois) / $antes * 100;
    }

    public static function aplicar(float $concentracaoInicial, float $taxaPercentual): float {
        if ($taxaPercentual < 0 || $taxaPercentual > 100) throw new InvalidArgumentException('Taxa 0-100');
        if ($concentracaoInicial < 0) throw new InvalidArgumentException('Concentracao inicial negativa');
        return $concentracaoInicial * (1 - $taxaPercentual / 100);
    }

    public static function multiCamadas(float $concentracaoInicial, array $taxas): float {
        $concentracao = $concentracaoInicial;
        foreach ($taxas as $taxa) $concentracao = self::aplicar($concentracao, (float)$taxa);
        return $concentracao;
    }

    public static function eficienciaGeral(array $antes, array $depois): array {
        $saida = [];
        foreach ($antes as $chave => $valor) {
            if (!array_key_exists($chave, $depois)) throw new InvalidArgumentException("Sem par $chave em depois");
            $saida[$chave] = self::taxaRemocao((float)$valor, (float)$depois[$chave]);
        }
        return $saida;
    }

    public static function classificarEficiencia(float $taxa): string {
        if ($taxa < 0) return 'piora';
        if ($taxa < 30) return 'baixa';
        if ($taxa < 70) return 'moderada';
        return 'alta';
    }
}
