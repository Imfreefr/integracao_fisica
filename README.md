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
- `WaterQualityClassifier`: pH 6-9.5, Turbidez ≤5 uT, Cloro 0.2-2, Dureza ≤500, TDS ≤500/1000, Temp ≤25/30. Fontes: Portaria GM/MS 888/2021, WHO GDWQ 4th.
- `Biofilter`: `removalRate=(antes-depois)/antes*100`, `apply`, `multiLayer`, `overallEfficiency`, `classifyEfficiency`.

## Dataset real
Substitua `data/samples.json` por medições reais de campo/lab. Nenhum dado fictício na entrega final.

## Estrutura
`src/WaterQualityClassifier.php` `src/Biofilter.php` `public/index.php` `tests/*`
