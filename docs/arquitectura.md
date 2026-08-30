# Arquitectura y coordenadas

## Alcance

El paquete resuelve una frontera concreta: FPDF trabaja con un sistema de coordenadas cómodo para maquetación, mientras que AutoFirma necesita las coordenadas de un rectángulo PDF para colocar una firma PAdES visible.

No se implementa la firma, AutoScript, el transporte intermedio ni la validación criptográfica.

## Componentes

### `FpdfAutoFirma`

Fachada pública y extensión de `FPDF`. Asocia cada recuadro a la página activa, valida que quepa en ella y conserva sus parámetros por nombre.

### `SignatureBox`

Value object con la geometría expresada en unidades FPDF y el texto de la firma visible. Rechaza definiciones inválidas antes de convertirlas.

### `CoordinateConverter`

Transforma coordenadas FPDF a coordenadas PDF sin depender de HTTP, WordPress ni AutoFirma. Al ser una clase pura puede probarse de forma determinista.

### `PdfRectangle`

Representa el rectángulo final en puntos PDF y con origen inferior izquierdo.

### `AutoFirmaParameters`

Construye el conjunto mínimo de extra parameters para una firma PAdES visible con texto.

## Conversión

FPDF usa el origen superior izquierdo y una coordenada `y` creciente hacia abajo. PDF usa el origen inferior izquierdo y `y` crece hacia arriba.

Para una página de altura `H`, un recuadro FPDF `(x, y, width, height)` y el factor de conversión de FPDF `k` hacia puntos PDF:

```text
lowerLeftX  = x * k
lowerLeftY  = (H - y - height) * k
upperRightX = (x + width) * k
upperRightY = (H - y) * k
```

El resultado se redondea al punto PDF más cercano. La geometría se valida antes de la conversión para evitar posiciones negativas o fuera de página.

## Separación respecto a otros proyectos

```text
FPDF AutoFirma (PHP)
        |
        | PDF + extra parameters
        v
@erseco/autofirma-client (browser)
        |
        v
AutoScript -> AutoFirma
        |
        | cuando AutoScript necesita transporte intermedio
        v
erseco/autofirma-intermediate-server
```

`wp-autofirma` es un consumidor específico para WordPress y una referencia de integración, pero no es una dependencia del núcleo.

## Decisiones de diseño

- Las áreas se registran sobre la página FPDF activa: evita tener que reconstruir tamaños y orientaciones de páginas anteriores.
- Los nombres son únicos para que una aplicación pueda seleccionar inequívocamente qué firma ejecutar.
- La API no expone un método `sign()` porque PHP no ejecuta AutoFirma.
- El núcleo no genera URLs, tokens ni sesiones de servidor intermedio.
- El núcleo no afirma que una firma sea válida; esa comprobación requiere validación criptográfica en servidor o un servicio de confianza adecuado.
