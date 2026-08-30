# Instrucciones para agentes

## Finalidad

Este repositorio implementa una extensión de FPDF para preparar documentos PDF destinados a firma PAdES con AutoFirma desde el navegador.

La librería no firma en PHP, no valida firmas electrónicas, no implementa criptografía, no incluye AutoFirma ni AutoScript y no debe presentarse como sustituto de una validación de firma en servidor.

## Idioma y estilo

- Código, identificadores, APIs, comentarios, docblocks y mensajes internos en inglés.
- README, documentación, ADR, plantillas y explicaciones para personas en español.
- Conserva los nombres oficiales de parámetros de AutoFirma/AutoScript aunque no sigan el estilo del proyecto.
- PHP con cuatro espacios, `declare(strict_types=1)` y PSR-12.
- JSON, YAML, Markdown y NEON con dos espacios.
- Mantén compatibilidad con PHP 7.4 salvo que una decisión de arquitectura documentada eleve expresamente el mínimo.

## Arquitectura

- `src/FpdfAutoFirma.php`: fachada pública que extiende `FPDF`.
- `src/SignatureBox.php`: geometría y texto en coordenadas FPDF.
- `src/CoordinateConverter.php`: conversión pura de FPDF a PDF.
- `src/PdfRectangle.php`: rectángulo final en puntos PDF.
- `src/AutoFirmaParameters.php`: extra parameters oficiales para firma visible.
- `src/Exception/`: errores específicos de la API pública.
- `tests/Unit/`: pruebas unitarias y de integración fina con FPDF.
- `docs/`: uso, arquitectura, calidad y publicación.

No dupliques responsabilidades de otros proyectos:

- `@erseco/autofirma-client` proporciona la integración de navegador con AutoScript y AutoFirma;
- `erseco/autofirma-intermediate-server` implementa el transporte intermedio cuando AutoScript lo necesita;
- `wp-autofirma` es una integración específica para WordPress y sirve como consumidor y referencia, no como dependencia del núcleo.

El núcleo debe seguir siendo independiente de WordPress, Symfony, Laravel y cualquier framework HTTP.

## API pública

- Evita métodos que sugieran falsamente que PHP ejecuta la firma, por ejemplo `sign()` o `signPdf()` si en realidad solo preparan datos.
- Las áreas de firma se expresan en el sistema de coordenadas y la unidad de FPDF.
- La conversión a PDF usa puntos y origen inferior izquierdo.
- Una caja se asocia a la página FPDF activa al registrarse.
- Los nombres de caja son únicos dentro del documento.
- Un cambio de nombres de métodos, claves devueltas, redondeo o sistema de coordenadas es un cambio observable y necesita tests y documentación.

## Análisis estático y FPDF

FPDF 1.x no declara tipos PHP en varios métodos y propiedades que esta librería necesita. Normaliza esos valores en la frontera de `FpdfAutoFirma` antes de pasarlos al núcleo tipado.

No bajes el nivel de PHPStan ni uses `ignoreErrors` para ocultar problemas derivados de la API no tipada de FPDF.

## Seguridad y límites

- No afirmes que una firma es válida solo porque un PDF contiene una firma.
- No aceptes datos devueltos por el navegador como prueba suficiente de identidad, autorización o validez jurídica.
- No incorpores claves privadas, certificados, credenciales ni material criptográfico al paquete.
- No añadas telemetría ni servicios remotos implícitos.
- Si se añaden ejemplos con servidor intermedio, usa tokens opacos y efímeros y remite a la documentación de seguridad del proyecto correspondiente.

## Pruebas y controles

Antes de publicar cambios ejecuta:

```bash
make install
make check
make coverage
```

`make check` valida Composer, PSR-12, PHPStan al nivel máximo, PHPUnit y auditoría. `make coverage` genera Clover y falla si la cobertura de sentencias baja del 90 %.

Todo cambio observable necesita pruebas. La conversión de coordenadas, límites de página, páginas múltiples, nombres duplicados y parámetros oficiales deben permanecer cubiertos.

La matriz de CI cubre PHP 7.4, 8.1, 8.4 y 8.5. La cobertura se calcula una vez con PCOV y se publica en Codecov mediante OIDC.

## Releases

- No declares `version` en `composer.json`.
- Los tags tienen forma `vX.Y.Z` y siguen SemVer.
- El tag es la única fuente de verdad de la versión publicada.
- El artefacto de release toma `X.Y.Z` directamente del tag.
- Una versión publicada no se modifica ni se reutiliza.
- El tag lo crea una persona, nunca un agente.
- Packagist obtiene las versiones de los tags mediante su integración con GitHub.

## Documentación

Actualiza `README.md` y el documento correspondiente de `docs/` cuando cambie la API o un comportamiento observable. Mantén los ejemplos ejecutables y no documentes capacidades que el código no implemente.

## Licencia

El proyecto usa licencia MIT. No copies código de proyectos con licencias incompatibles solo para reutilizar una implementación; reimplementa la interfaz necesaria o usa la dependencia correspondiente respetando su licencia.
