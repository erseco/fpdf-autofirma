# Publicación

## Packagist

El paquete está preparado para publicarse como:

```text
erseco/fpdf-autofirma
```

`composer.json` no contiene `version`. Packagist debe obtener las versiones de los tags Git del repositorio.

## Tags

Las versiones estables siguen SemVer y usan tags:

```text
v0.1.0
v0.2.0
v1.0.0
```

El tag es la única fuente de verdad. No hay que editar una versión duplicada antes de publicar.

## GitHub Release

Un push de `vX.Y.Z` ejecuta `.github/workflows/release.yml`:

1. valida el formato del tag;
2. instala dependencias;
3. ejecuta `make check`;
4. extrae `X.Y.Z` del propio tag;
5. crea `fpdf-autofirma-X.Y.Z.zip` con `composer archive`;
6. comprueba que el ZIP incluye `composer.json` y `src/FpdfAutoFirma.php` y excluye archivos de desarrollo;
7. crea la GitHub Release con el ZIP ya comprobado.

El workflow no crea tags ni modifica `composer.json`.

## Primera publicación

Antes del primer tag estable conviene comprobar:

```bash
make check
make coverage
```

La primera versión estable debe publicarse solo cuando la API documentada tenga pruebas y el flujo FPDF → parámetros AutoFirma se haya verificado con una integración real de AutoScript/AutoFirma.
