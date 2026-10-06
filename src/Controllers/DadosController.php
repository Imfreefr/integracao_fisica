<?php
declare(strict_types=1);

namespace App\Controllers;
//Controller dos dados enviados para a plataforma
final class DadosController
{
    public function csv(array $entrada): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dataset-agua-ods6.csv"');
        $saida = fopen('php://output', 'wb');
        fputcsv($saida, ['parametro', 'antes', 'depois'], ',', '"', '\\');
        $campos = [['ph_antes', 'ph_depois', 'pH'], ['turb_antes', 'turb_depois', 'Turbidez (uT)'], ['cloro_antes', 'cloro_depois', 'Cloro (mg/L)'], ['dur_antes', 'dur_depois', 'Dureza (mg/L)'], ['temp_antes', 'temp_depois', 'Temperatura (°C)'], ['solidos_antes', 'solidos_depois', 'TDS (mg/L)']];
        foreach ($campos as [$antes, $depois, $rotulo]) fputcsv($saida, [$rotulo, $entrada[$antes] ?? '', $entrada[$depois] ?? ''], ',', '"', '\\');
        fclose($saida);
    }
}
