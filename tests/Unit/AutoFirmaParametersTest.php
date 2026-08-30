<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Tests\Unit;

use Erseco\FpdfAutoFirma\AutoFirmaParameters;
use Erseco\FpdfAutoFirma\PdfRectangle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AutoFirmaParametersTest extends TestCase
{
    public function testBuildsOfficialVisibleSignatureParameters(): void
    {
        $parameters = AutoFirmaParameters::fromVisibleSignature(
            2,
            new PdfRectangle(10, 20, 30, 40),
            'Signed by $$SUBJECTCN$$'
        );

        self::assertSame([
            'signaturePositionOnPageLowerLeftX' => 10,
            'signaturePositionOnPageLowerLeftY' => 20,
            'signaturePositionOnPageUpperRightX' => 30,
            'signaturePositionOnPageUpperRightY' => 40,
            'signaturePage' => 2,
            'layer2Text' => 'Signed by $$SUBJECTCN$$',
        ], $parameters->toArray());
    }

    public function testRejectsInvalidPage(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AutoFirmaParameters::fromVisibleSignature(0, new PdfRectangle(0, 0, 10, 10), 'Text');
    }

    public function testRejectsEmptyText(): void
    {
        $this->expectException(InvalidArgumentException::class);

        AutoFirmaParameters::fromVisibleSignature(1, new PdfRectangle(0, 0, 10, 10), ' ');
    }
}
