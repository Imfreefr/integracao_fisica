<?php
declare(strict_types=1);
namespace App;

use InvalidArgumentException;

final class ClassificadorQualidadeAgua
{
    public static function classificarPh(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('pH ausente');
        if ($valor < 0 || $valor > 14) throw new InvalidArgumentException('pH fisicamente impossível: '.$valor);
        if ($valor >= 6.0 && $valor <= 9.5) return ['parametro'=>'pH','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Potável'];
        return ['parametro'=>'pH','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não potável'];
    }

    public static function classificarTurbidez(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('Turbidez ausente');
        if ($valor < 0) throw new InvalidArgumentException('Turbidez negativa');
        if ($valor <= 5.0) return ['parametro'=>'turbidez','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Potável'];
        return ['parametro'=>'turbidez','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não potável'];
    }

    public static function classificarCloro(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('Cloro ausente');
        if ($valor < 0) throw new InvalidArgumentException('Cloro negativo');
        if ($valor >= 0.2 && $valor <= 2.0) return ['parametro'=>'cloro','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Potável'];
        if ($valor > 2.0 && $valor <= 5.0) return ['parametro'=>'cloro','valor'=>$valor,'situacao'=>'alerta','rotulo'=>'Alerta - acima do recomendado'];
        return ['parametro'=>'cloro','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não potável'];
    }

    public static function classificarDureza(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('Dureza ausente');
        if ($valor < 0) throw new InvalidArgumentException('Dureza negativa');
        if ($valor <= 500) return ['parametro'=>'dureza','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Potável'];
        return ['parametro'=>'dureza','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não potável'];
    }

    public static function classificarTemperatura(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('Temperatura ausente');
        if ($valor < -10 || $valor > 100) throw new InvalidArgumentException('Temperatura fisicamente improvável');
        if ($valor <= 25) return ['parametro'=>'temperatura','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Adequada'];
        if ($valor <= 30) return ['parametro'=>'temperatura','valor'=>$valor,'situacao'=>'alerta','rotulo'=>'Alerta'];
        return ['parametro'=>'temperatura','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não adequada'];
    }

    public static function classificarSolidosTotais(?float $valor): array {
        if ($valor === null) throw new InvalidArgumentException('Solidos totais ausente');
        if ($valor < 0) throw new InvalidArgumentException('Solidos totais negativo');
        if ($valor <= 500) return ['parametro'=>'solidosTotais','valor'=>$valor,'situacao'=>'potavel','rotulo'=>'Potável'];
        if ($valor <= 1000) return ['parametro'=>'solidosTotais','valor'=>$valor,'situacao'=>'alerta','rotulo'=>'Alerta'];
        return ['parametro'=>'solidosTotais','valor'=>$valor,'situacao'=>'nao_potavel','rotulo'=>'Não potável'];
    }

    public static function avaliarAmostra(array $dados): array {
        $obrigatorios = ['ph','turbidez','cloro','dureza','temperatura','solidosTotais'];
        foreach ($obrigatorios as $chave) {
            if (!array_key_exists($chave, $dados)) throw new InvalidArgumentException("Campo ausente: $chave");
        }
        $classificacoes = [
            'ph' => self::classificarPh((float)$dados['ph']),
            'turbidez' => self::classificarTurbidez((float)$dados['turbidez']),
            'cloro' => self::classificarCloro((float)$dados['cloro']),
            'dureza' => self::classificarDureza((float)$dados['dureza']),
            'temperatura' => self::classificarTemperatura((float)$dados['temperatura']),
            'solidosTotais' => self::classificarSolidosTotais((float)$dados['solidosTotais']),
        ];
        $situacoes = array_column($classificacoes, 'situacao');
        $parecer = in_array('nao_potavel', $situacoes, true) ? 'NAO_POTAVEL' : (in_array('alerta', $situacoes, true) ? 'ALERTA' : 'POTAVEL');
        return ['parametros'=>$classificacoes,'parecer'=>$parecer];
    }
}
