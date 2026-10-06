<?php
declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

final class ClassificadorQualidadeAgua
{
    public static function classificarPh(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('pH ausente');
        if ($valor < 0 || $valor > 14) throw new InvalidArgumentException('pH fisicamente impossível: ' . $valor);
        return $valor >= 6 && $valor <= 9.5 ? self::resultado('pH', $valor, 'potavel', 'Potável') : self::resultado('pH', $valor, 'nao_potavel', 'Não potável');
    }

    public static function classificarTurbidez(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('Turbidez ausente');
        if ($valor < 0) throw new InvalidArgumentException('Turbidez negativa');
        return $valor <= 5 ? self::resultado('turbidez', $valor, 'potavel', 'Potável') : self::resultado('turbidez', $valor, 'nao_potavel', 'Não potável');
    }

    public static function classificarCloro(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('Cloro ausente');
        if ($valor < 0) throw new InvalidArgumentException('Cloro negativo');
        if ($valor >= .2 && $valor <= 2) return self::resultado('cloro', $valor, 'potavel', 'Potável');
        if ($valor <= 5) return self::resultado('cloro', $valor, 'alerta', 'Alerta - acima do recomendado');
        return self::resultado('cloro', $valor, 'nao_potavel', 'Não potável');
    }

    public static function classificarDureza(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('Dureza ausente');
        if ($valor < 0) throw new InvalidArgumentException('Dureza negativa');
        return $valor <= 500 ? self::resultado('dureza', $valor, 'potavel', 'Potável') : self::resultado('dureza', $valor, 'nao_potavel', 'Não potável');
    }

    public static function classificarTemperatura(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('Temperatura ausente');
        if ($valor < -10 || $valor > 100) throw new InvalidArgumentException('Temperatura fisicamente improvável');
        if ($valor <= 25) return self::resultado('temperatura', $valor, 'potavel', 'Adequada');
        if ($valor <= 30) return self::resultado('temperatura', $valor, 'alerta', 'Alerta');
        return self::resultado('temperatura', $valor, 'nao_potavel', 'Não adequada');
    }

    public static function classificarSolidosTotais(?float $valor): array
    {
        if ($valor === null) throw new InvalidArgumentException('Sólidos totais ausente');
        if ($valor < 0) throw new InvalidArgumentException('Sólidos totais negativo');
        if ($valor <= 500) return self::resultado('solidosTotais', $valor, 'potavel', 'Potável');
        if ($valor <= 1000) return self::resultado('solidosTotais', $valor, 'alerta', 'Alerta');
        return self::resultado('solidosTotais', $valor, 'nao_potavel', 'Não potável');
    }

    public static function avaliarAmostra(array $dados): array
    {
        $obrigatorios = ['ph', 'turbidez', 'cloro', 'dureza', 'temperatura', 'solidosTotais'];
        foreach ($obrigatorios as $chave) if (!array_key_exists($chave, $dados)) throw new InvalidArgumentException("Campo ausente: $chave");
        $parametros = [
            'ph' => self::classificarPh((float) $dados['ph']),
            'turbidez' => self::classificarTurbidez((float) $dados['turbidez']),
            'cloro' => self::classificarCloro((float) $dados['cloro']),
            'dureza' => self::classificarDureza((float) $dados['dureza']),
            'temperatura' => self::classificarTemperatura((float) $dados['temperatura']),
            'solidosTotais' => self::classificarSolidosTotais((float) $dados['solidosTotais']),
        ];
        $situacoes = array_column($parametros, 'situacao');
        $parecer = in_array('nao_potavel', $situacoes, true) ? 'NAO_POTAVEL' : (in_array('alerta', $situacoes, true) ? 'ALERTA' : 'POTAVEL');
        return ['parametros' => $parametros, 'parecer' => $parecer];
    }

    private static function resultado(string $parametro, float $valor, string $situacao, string $rotulo): array
    {
        return compact('parametro', 'valor', 'situacao', 'rotulo');
    }
}
