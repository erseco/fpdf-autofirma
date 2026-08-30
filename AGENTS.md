# Instrucciones para agentes

## Finalidad

Este repositorio implementará una extensión de FPDF para preparar documentos PDF destinados a firma PAdES con AutoFirma desde el navegador.

La librería no firma en PHP, no valida firmas electrónicas, no implementa criptografía, no incluye AutoFirma ni AutoScript y no debe presentarse como sustituto de una validación de firma en servidor.

## Idioma y estilo

- Código, identificadores, APIs, comentarios, docblocks y mensajes internos en inglés.
- README, documentación, ADR, plantillas y explicaciones para personas en español.
- Conserva los nombres oficiales de parámetros de AutoFirma/AutoScript aunque no sigan el estilo del proyecto.
- PHP con cuatro espacios, `declare(strict_types=1)` y PSR-12.
- JSON, YAML y Markdown con dos espacios.
- Mantén compatibilidad con PHP 7.4 salvo que una decisión de arquitectura documentada eleve expresamente el mínimo.

## Arquitectura

La responsabilidad del proyecto debe limitarse a la integración específica con FPDF:

- representar áreas de firma visible en coordenadas y unidades de FPDF;
- convertirlas al sistema de coordenadas y unidades esperado por PDF/AutoFirma;
- construir los parámetros PAdES necesarios para la firma visible;
- exponer el PDF y los parámetros de forma que una capa de navegador pueda firmarlos.

No dupliques responsabilidades de otros proyectos:

- `@erseco/autofirma-client` proporciona la integración de navegador con AutoScript y AutoFirma;
- `erseco/autofirma-intermediate-server` implementa el transporte intermedio cuando AutoScript lo necesita;
- `wp-autofirma` es una integración específica para WordPress y sirve como consumidor y referencia, no como dependencia del núcleo.

El núcleo debe seguir siendo independiente de WordPress, Symfony, Laravel y cualquier framework HTTP.

## API pública

- Evita métodos que sugieran falsamente que PHP ejecuta la firma, por ejemplo `sign()` o `signPdf()` si en realidad solo preparan datos.
- Prefiere nombres que expresen preparación, parámetros o áreas de firma.
- La conversión de coordenadas debe ser determinista y estar cubierta por pruebas antes de considerarse estable.
- No expongas detalles internos innecesarios de FPDF si pueden encapsularse.
- Mantén compatibilidad con `setasign/fpdf` y documenta cualquier dependencia de una versión concreta.

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
```

Mientras el repositorio solo contiene infraestructura, `make check` valida Composer y audita dependencias. Antes de publicar la primera versión funcional debe incluir como mínimo pruebas unitarias de conversión de coordenadas y generación de parámetros, estilo y análisis estático.

## Releases

- No declares `version` en `composer.json`.
- Los tags tienen forma `vX.Y.Z` y siguen SemVer.
- El tag es la única fuente de verdad de la versión publicada.
- El artefacto de release toma `X.Y.Z` directamente del tag.
- Una versión publicada no se modifica ni se reutiliza.
- El tag lo crea una persona, nunca un agente.
- Packagist obtiene las versiones de los tags mediante su integración con GitHub.
- No publiques una versión estable hasta que la API pública, las pruebas y los ejemplos funcionales estén preparados.

## Licencia

El proyecto usa licencia MIT. No copies código de proyectos con licencias incompatibles solo para reutilizar una implementación; reimplementa la interfaz necesaria o usa la dependencia correspondiente respetando su licencia.
