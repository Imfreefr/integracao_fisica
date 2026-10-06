<?php
declare(strict_types=1);

namespace App\Models;

final class AmostraRepository
{
    public function __construct(private readonly string $arquivo) {}

    public function salvar(array $amostra): void
    {
        $amostras = $this->todas();
        $amostras[] = $amostra;
        @mkdir(dirname($this->arquivo), 0777, true);
        file_put_contents($this->arquivo, json_encode($amostras, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function todas(): array
    {
        if (!is_file($this->arquivo) || filesize($this->arquivo) === 0) return [];
        $dados = json_decode((string) file_get_contents($this->arquivo), true);
        return !is_array($dados) ? [] : (isset($dados['antes']) ? [$dados] : $dados);
    }
}
