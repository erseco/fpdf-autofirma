# Calidad y cobertura

## Controles locales

```bash
make install
make check
```

`make check` ejecuta la misma puerta de calidad que la matriz principal de CI:

1. `composer validate --strict`;
2. PHPCS con PSR-12;
3. PHPStan al nivel máximo;
4. PHPUnit;
5. `composer audit`.

Para aplicar las correcciones automáticas disponibles de PHPCS:

```bash
make fix
```

## Cobertura

```bash
make coverage
```

PHPUnit genera `coverage.xml` en formato Clover. Después `scripts/check-coverage.php` calcula la cobertura de sentencias del proyecto y devuelve error si es inferior al **90 %**.

El umbral local evita depender exclusivamente del estado que calcule un servicio externo. Si baja del 90 %, CI falla antes de subir el informe.

## Codecov

El job `coverage` de GitHub Actions usa PCOV en PHP 8.4 y publica `coverage.xml` en Codecov mediante OIDC, sin un token permanente guardado en el repositorio.

`codecov.yml` exige también un 90 % tanto para el proyecto como para el código modificado.

## Matriz de PHP

La CI ejecuta los tests y controles en:

- PHP 7.4;
- PHP 8.1;
- PHP 8.4;
- PHP 8.5.

La cobertura solo se calcula una vez para evitar ejecutar instrumentación en toda la matriz.
