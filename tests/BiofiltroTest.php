<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Biofiltro;

final class BiofiltroTest extends TestCase {
    public function testRemocaoFeliz(): void {
        $this->assertEqualsWithDelta(80.0, Biofiltro::taxaRemocao(100, 20), 0.001);
    }
    public function testRemocaoManual(): void {
        $this->assertEqualsWithDelta(60.0, Biofiltro::taxaRemocao(50, 20), 0.001);
    }
    public function testRemocaoZeroDepois(): void {
        $this->assertEqualsWithDelta(100.0, Biofiltro::taxaRemocao(10, 0), 0.001);
    }
    public function testRemocaoZeroAntesLancaExcecao(): void {
        $this->expectException(InvalidArgumentException::class);
        Biofiltro::taxaRemocao(0, 5);
    }
    public function testRemocaoNegativaLancaExcecao(): void {
        $this->expectException(InvalidArgumentException::class);
        Biofiltro::taxaRemocao(-1, 5);
    }
    public function testAplicar(): void {
        $this->assertEqualsWithDelta(80.0, Biofiltro::aplicar(100, 20), 0.001);
    }
    public function testAplicarTaxaInvalida(): void {
        $this->expectException(InvalidArgumentException::class);
        Biofiltro::aplicar(100, 101);
    }
    public function testMultiCamadas(): void {
        $concentracao = Biofiltro::multiCamadas(100, [50, 50]);
        $this->assertEqualsWithDelta(25.0, $concentracao, 0.001);
    }
    public function testMultiCamadasVazio(): void {
        $this->assertEqualsWithDelta(100.0, Biofiltro::multiCamadas(100, []), 0.001);
    }
    public function testEficienciaGeral(): void {
        $antes = ['turbidez' => 10, 'solidosTotais' => 500];
        $depois = ['turbidez' => 2, 'solidosTotais' => 250];
        $resultado = Biofiltro::eficienciaGeral($antes, $depois);
        $this->assertEqualsWithDelta(80.0, $resultado['turbidez'], 0.001);
        $this->assertEqualsWithDelta(50.0, $resultado['solidosTotais'], 0.001);
    }
    public function testEficienciaAusenteLancaExcecao(): void {
        $this->expectException(InvalidArgumentException::class);
        Biofiltro::eficienciaGeral(['a' => 10], ['b' => 5]);
    }
    public function testClassificarEficiencia(): void {
        $this->assertSame('piora', Biofiltro::classificarEficiencia(-5));
        $this->assertSame('baixa', Biofiltro::classificarEficiencia(10));
        $this->assertSame('moderada', Biofiltro::classificarEficiencia(50));
        $this->assertSame('alta', Biofiltro::classificarEficiencia(80));
    }
    public function testIntegracaoComClassificador(): void {
        $antes = ['ph' => 7, 'turbidez' => 8, 'cloro' => 0.1, 'dureza' => 600, 'temperatura' => 28, 'solidosTotais' => 800];
        $depois = ['ph' => 7.2, 'turbidez' => 2, 'cloro' => 1.0, 'dureza' => 300, 'temperatura' => 24, 'solidosTotais' => 400];
        $eficiencia = Biofiltro::eficienciaGeral(['turbidez' => 8, 'solidosTotais' => 800], ['turbidez' => 2, 'solidosTotais' => 400]);
        $this->assertGreaterThan(0, $eficiencia['turbidez']);
        $avaliacaoAntes = \App\ClassificadorQualidadeAgua::avaliarAmostra($antes);
        $avaliacaoDepois = \App\ClassificadorQualidadeAgua::avaliarAmostra($depois);
        $this->assertSame('NAO_POTAVEL', $avaliacaoAntes['parecer']);
        $this->assertSame('POTAVEL', $avaliacaoDepois['parecer']);
    }
}
