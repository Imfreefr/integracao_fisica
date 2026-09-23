# Lab Água - Qualidade + Biofiltro (ODS 6)

## Requisitos
PHP >=8.3, Composer, Laravel Herd (ou `php -S localhost:8000 -t public`)

## Instalar
```
composer install
```

## Rodar
Herd: aponte para `public` ou `php -S localhost:8000 -t public` e abra http://localhost:8000

## Algoritmos
- `Medidor de Qualidade de Água`: pH 6-9.5, Turbidez ≤5 uT, Cloro 0.2-2, Dureza ≤500, TDS ≤500/1000, Temp ≤25/30. Fontes: Portaria GM/MS 888/2021, WHO GDWQ 4th.
## Dataset real
Substitua `data/samples.json` por medições reais de campo/lab. Nenhum dado fictício na entrega final.

## Estrutura
`src/WaterQualityClassifier.php` `src/Biofilter.php` `public/index.php` `tests/*`

## Uso de IA
Design e repaginação: Uso do lovable para escolher a paleta de cores, organização dos elementos e como cada um dos dados deveria ser mostrado;
Testes: Pedi para o chat gpt desenvolver um **roteiro** de todos os possíveis testes que deveriam ser feitos (tanto testes de eficiência da plataforma quanto em casos onde haver campos vazios), depois escrevi os testes e gerei um cache de seus resultados, ao todo, o resultado foi este:
32 testes (47 asserts) em 2 classes;
ClassificadorQualidadeAgua 19: pH 6, turbidez 3, cloro 3, dureza/tds/temp 3, avaliar 3 + campo ausente.;
Biofiltro 13: taxa 5, aplicar 2, multiCamadas 2, eficiência 2, classificar 1, integração 1.;
