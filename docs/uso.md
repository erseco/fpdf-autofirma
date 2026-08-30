# Uso e integración

## Crear un área de firma

`FpdfAutoFirma` extiende la clase `FPDF`, por lo que conserva su API habitual. El recuadro de firma se define después de crear la página en la que debe aparecer:

```php
use Erseco\FpdfAutoFirma\FpdfAutoFirma;

$pdf = new FpdfAutoFirma();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, 'Documento para firma');

$pdf->addSignatureBox(
    'approval',
    130,
    240,
    60,
    25,
    'Firmado por $$SUBJECTCN$$ el $$SIGNDATE=dd/MM/yyyy$$'
);
```

Los argumentos geométricos usan exactamente la misma unidad configurada al crear FPDF (`mm` por defecto). `x` e `y` indican la esquina superior izquierda del recuadro, igual que el resto de operaciones de posicionamiento de FPDF.

El nombre identifica el recuadro dentro del documento y debe ser único.

## Obtener los parámetros de AutoFirma

```php
$parameters = $pdf->getAutoFirmaParameters('approval');
```

El resultado contiene los parámetros oficiales necesarios para una firma PAdES visible:

```text
signaturePositionOnPageLowerLeftX
signaturePositionOnPageLowerLeftY
signaturePositionOnPageUpperRightX
signaturePositionOnPageUpperRightY
signaturePage
layer2Text
```

Las coordenadas se entregan en puntos PDF y con origen en la esquina inferior izquierda.

## Generar el PDF

FPDF puede devolver el documento como cadena:

```php
$pdfData = $pdf->Output('S');
```

Una aplicación web puede preparar una respuesta para su frontend, por ejemplo:

```php
$payload = [
    'data' => base64_encode($pdfData),
    'parameters' => $parameters,
];
```

Codificar y transportar el documento no forma parte del núcleo de esta librería; el ejemplo solo muestra una forma habitual de enlazar PHP con el navegador.

## Firmar en el navegador

La firma real debe ejecutarse en el navegador mediante AutoScript/AutoFirma. Con `@erseco/autofirma-client`, la aplicación consumidora puede usar los parámetros generados por esta librería como `parameters` de una operación PAdES.

FPDF AutoFirma no incluye `autoscript.js` ni depende del paquete JavaScript. Esto mantiene el paquete PHP independiente del frontend elegido por cada aplicación.

## Varias áreas

Cada área conserva la página activa en el momento de su creación:

```php
$pdf->AddPage();
$pdf->addSignatureBox('first', 20, 240, 70, 25, 'Primera firma: $$SUBJECTCN$$');

$pdf->AddPage();
$pdf->addSignatureBox('second', 120, 240, 70, 25, 'Segunda firma: $$SUBJECTCN$$');

$all = $pdf->getAllAutoFirmaParameters();
```

También están disponibles:

```php
$pdf->hasSignatureBox('first');
$pdf->getSignatureBoxNames();
```

## Errores

La librería rechaza antes de generar parámetros:

- nombres vacíos o duplicados;
- texto visible vacío;
- coordenadas negativas o no finitas;
- ancho o alto iguales o inferiores a cero;
- recuadros que queden fuera de la página;
- consultas a recuadros inexistentes;
- intentos de añadir un recuadro antes de crear una página FPDF.

Estas comprobaciones validan la geometría y la coherencia de los parámetros. No validan certificados ni firmas electrónicas.
