# Laboratório Digital — Qualidade da Água

Aplicação PHP para comparar amostras de água antes e depois de um biofiltro, relacionando os resultados ao ODS 6.

## Requisitos

- PHP >= 8.4
- Composer
- Laravel Herd (opcional) ou o servidor embutido do PHP

## Instalação e execução

```bash
composer install
php -S localhost:8000
```

Abra `http://localhost:8000`. No Laravel Herd, aponte o site para a raiz deste repositório, onde está o `index.php`.

## Testes

```bash
vendor/bin/phpunit --testdox
vendor/bin/phpunit --coverage-text
```

A cobertura deve ser conferida para manter pelo menos 80% nas classes de algoritmo em `src/Models`. A cobertura da interface não faz parte da meta.

## Algoritmos

- `ClassificadorQualidadeAgua`: classifica pH, turbidez, cloro residual, dureza, temperatura e sólidos totais; também produz o parecer geral da amostra.
- `Biofiltro`: calcula taxa de remoção, aplica uma taxa, simula múltiplas camadas e classifica a eficiência.
- `AmostraRepository`: grava as amostras processadas em `data/samples.json`.

## Faixas de referência

- pH: 6,0 a 9,5.
- Turbidez: até 5 uT.
- Cloro residual livre: mínimo de 0,2 mg/L e limite superior de 5 mg/L; 0,2 a 2 mg/L é tratado como faixa recomendada.
- Dureza: até 500 mg/L, usada como referência físico-química.
- Sólidos totais: até 500 mg/L como faixa preferencial e até 1000 mg/L como faixa de alerta do projeto.
- Temperatura: até 25 °C como faixa adequada e 26–30 °C como alerta operacional; não é apresentada como limite legal de potabilidade.

As referências principais são a [Portaria GM/MS nº 888/2021](https://bvsms.saude.gov.br/bvs/saudelegis/gm/2021/prt0888_07_05_2021.html) e as [Diretrizes da OMS para qualidade da água potável](https://www.who.int/publications/i/item/9789241549950). A classificação de temperatura e as faixas preferenciais são critérios didáticos do projeto e devem ser discutidas no relatório técnico.

## Dataset

O sistema grava os dados submetidos em `data/samples.json` e permite baixar um CSV pela interface. Antes da entrega, deve ser inserido nesse arquivo o dataset real coletado pela equipe, sem preencher a base com valores de exemplo ou dados inventados. O arquivo pode ser versionado junto com o trabalho conforme a orientação do professor.

## Estrutura

- `src/Models`: algoritmos e persistência.
- `src/Views`: formulário e resultados.
- `src/Controllers`: processamento da aplicação e exportação CSV.
- `tests`: testes unitários e de integração simples.
