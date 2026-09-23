<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\ClassificadorQualidadeAgua;

final class ClassificadorQualidadeAguaTest extends TestCase {
    public function testPhFeliz(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarPh(7.0)['situacao']);
    }
    public function testPhBordaInferior(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarPh(6.0)['situacao']);
    }
    public function testPhBordaSuperior(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarPh(9.5)['situacao']);
    }
    public function testPhFora(): void {
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarPh(5.9)['situacao']);
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarPh(9.6)['situacao']);
    }
    public function testPhImpossivel(): void {
        $this->expectException(InvalidArgumentException::class);
        ClassificadorQualidadeAgua::classificarPh(-1);
    }
    public function testPhNulo(): void {
        $this->expectException(InvalidArgumentException::class);
        ClassificadorQualidadeAgua::classificarPh(null);
    }
    public function testTurbidezFeliz(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarTurbidez(2)['situacao']);
    }
    public function testTurbidezBorda(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarTurbidez(5.0)['situacao']);
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarTurbidez(5.01)['situacao']);
    }
    public function testTurbidezNegativa(): void {
        $this->expectException(InvalidArgumentException::class);
        ClassificadorQualidadeAgua::classificarTurbidez(-0.1);
    }
    public function testCloroFeliz(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarCloro(1.0)['situacao']);
    }
    public function testCloroBordas(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarCloro(0.2)['situacao']);
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarCloro(2.0)['situacao']);
        $this->assertSame('alerta', ClassificadorQualidadeAgua::classificarCloro(2.01)['situacao']);
    }
    public function testCloroBaixo(): void {
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarCloro(0.1)['situacao']);
    }
    public function testDurezaBorda(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarDureza(500)['situacao']);
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarDureza(500.01)['situacao']);
    }
    public function testSolidosBordas(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarSolidosTotais(500)['situacao']);
        $this->assertSame('alerta', ClassificadorQualidadeAgua::classificarSolidosTotais(501)['situacao']);
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarSolidosTotais(1001)['situacao']);
    }
    public function testTemperatura(): void {
        $this->assertSame('potavel', ClassificadorQualidadeAgua::classificarTemperatura(25)['situacao']);
        $this->assertSame('alerta', ClassificadorQualidadeAgua::classificarTemperatura(28)['situacao']);
        $this->assertSame('nao_potavel', ClassificadorQualidadeAgua::classificarTemperatura(31)['situacao']);
    }
    public function testAvaliarPotavel(): void {
        $dados = ['ph' => 7, 'turbidez' => 2, 'cloro' => 1, 'dureza' => 200, 'temperatura' => 22, 'solidosTotais' => 300];
        $this->assertSame('POTAVEL', ClassificadorQualidadeAgua::avaliarAmostra($dados)['parecer']);
    }
    public function testAvaliarAlerta(): void {
        $dados = ['ph' => 7, 'turbidez' => 2, 'cloro' => 1, 'dureza' => 200, 'temperatura' => 28, 'solidosTotais' => 600];
        $this->assertSame('ALERTA', ClassificadorQualidadeAgua::avaliarAmostra($dados)['parecer']);
    }
    public function testAvaliarNaoPotavel(): void {
        $dados = ['ph' => 5, 'turbidez' => 2, 'cloro' => 1, 'dureza' => 200, 'temperatura' => 22, 'solidosTotais' => 300];
        $this->assertSame('NAO_POTAVEL', ClassificadorQualidadeAgua::avaliarAmostra($dados)['parecer']);
    }
    public function testAvaliarCampoAusente(): void {
        $this->expectException(InvalidArgumentException::class);
        ClassificadorQualidadeAgua::avaliarAmostra(['ph' => 7]);
    }
}
