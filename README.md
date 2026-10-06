# Laboratório Digital — Qualidade da Água

Aplicação PHP puro para comparar amostras de água antes e depois de um biofiltro, com referência à Portaria GM/MS 888/2021 (ODS 6).

## Requisitos

- PHP >= 8.4
- Composer

## Instalação e execução

```bash
composer install
php -S localhost:8000 -t public
```

Acesse `http://localhost:8000`.

## Estrutura MVC

- `src/Models`: regras de classificação, biofiltro e persistência das amostras.
- `src/Views`: formulário e apresentação dos resultados.
- `src/Controllers`: processamento do formulário e exportação CSV.
- `index.php` e `data.php`: pontos de entrada da aplicação na raiz.
- `src/css`: folha de estilos da aplicação.
- `tests`: testes automatizados dos modelos.

O projeto não utiliza JavaScript: o cálculo e o download do CSV são processados pelo PHP.



Os dados enviados são gravados em `data/samples.json`. (Por isso a pasta data que está vazia agora)
