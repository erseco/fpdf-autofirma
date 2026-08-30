# FPDF AutoFirma

[![CI](https://github.com/erseco/fpdf-autofirma/actions/workflows/ci.yml/badge.svg)](https://github.com/erseco/fpdf-autofirma/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/erseco/fpdf-autofirma.svg)](https://packagist.org/packages/erseco/fpdf-autofirma)
[![PHP](https://img.shields.io/packagist/php-v/erseco/fpdf-autofirma.svg)](https://packagist.org/packages/erseco/fpdf-autofirma)
[![License](https://img.shields.io/github/license/erseco/fpdf-autofirma)](LICENSE)

Extension for the FPDF class ([www.fpdf.org](https://www.fpdf.org/)) providing AutoFirma integration, visible PAdES signature areas, and signing parameters for browser-based electronic signatures.

**Author:** [@erseco](https://github.com/erseco)  
**License:** MIT License — same as FPDF.

Integración de FPDF con AutoFirma para preparar documentos PDF que se firmarán con PAdES desde el navegador.

> [!IMPORTANT]
> Este proyecto es independiente y no oficial. No pertenece al Gobierno de España, no incluye AutoFirma ni AutoScript y no realiza ni valida firmas electrónicas por sí solo.

## Estado

El proyecto está en desarrollo. La infraestructura del paquete y de publicación está preparada, pero la API pública todavía no se considera estable. No debe publicarse una versión estable hasta que existan implementación, pruebas y ejemplos funcionales.

## Objetivo

La librería debe ocuparse únicamente de la parte específica de FPDF:

- definir áreas de firma visible usando las coordenadas y unidades de FPDF;
- convertir esas coordenadas al sistema que espera PDF/AutoFirma;
- generar los parámetros PAdES que necesita AutoScript;
- permitir que una aplicación entregue el PDF y esos parámetros a la capa de firma del navegador.

La firma no debe ejecutarse en PHP. AutoFirma trabaja en el equipo de la persona usuaria y necesita la capa de navegador proporcionada por AutoScript.

## Arquitectura

La separación prevista es:

```text
FPDF AutoFirma (PHP)
        |
        | PDF + parámetros PAdES
        v
@erseco/autofirma-client (navegador)
        |
        v
AutoScript -> AutoFirma
        |
        | cuando el protocolo necesita servidor intermedio, por ejemplo en móvil
        v
erseco/autofirma-intermediate-server
```

Este paquete no debe depender de WordPress ni incorporar el servidor intermedio. Las integraciones con aplicaciones concretas pertenecen a los proyectos consumidores.

## Instalación

Cuando el paquete esté publicado en Packagist:

```bash
composer require erseco/fpdf-autofirma
```

La dependencia de FPDF se resuelve mediante `setasign/fpdf`.

## Desarrollo

```bash
make install
make check
```

La infraestructura inicial valida `composer.json` y audita dependencias. Cuando se añada la implementación PHP, `make check` deberá ampliarse con estilo, análisis estático y pruebas antes de publicar la primera versión.

## Versionado y publicación

`composer.json` no contiene una propiedad `version`. Las versiones publicadas se obtienen exclusivamente de tags Git con formato SemVer:

```text
v0.1.0
v0.2.0
v1.0.0
```

Al hacer push de un tag `vX.Y.Z`, GitHub Actions:

1. valida que el tag tenga formato SemVer;
2. ejecuta los controles del proyecto;
3. obtiene `X.Y.Z` directamente del tag;
4. genera `fpdf-autofirma-X.Y.Z.zip`;
5. crea una GitHub Release con ese mismo artefacto.

Packagist obtiene la versión del tag mediante su integración con GitHub. El workflow no modifica `composer.json` ni mantiene una segunda fuente de verdad para la versión.

## Idioma

La documentación orientada a personas usuarias se mantiene en español porque AutoFirma está dirigido principalmente al ecosistema administrativo español. El código, identificadores, comentarios, docblocks y mensajes internos se mantienen en inglés.

## Licencia

MIT. FPDF se distribuye también bajo licencia MIT. AutoFirma, AutoScript y las demás dependencias mantienen sus propias licencias y marcas.
