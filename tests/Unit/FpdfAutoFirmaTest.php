<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Tests\Unit;

use Erseco\FpdfAutoFirma\Exception\DuplicateSignatureBox;
use Erseco\FpdfAutoFirma\Exception\InvalidSignatureBox;
use Erseco\FpdfAutoFirma\Exception\UnknownSignatureBox;
use Erseco\FpdfAutoFirma\FpdfAutoFirma;
use LogicException;
use PHPUnit\Framework\TestCase;

final class FpdfAutoFirmaTest extends TestCase
{
    public function testRequiresActivePage(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);

        $this->expectException(LogicException::class);
        $pdf->addSignatureBox('approval', 10.0, 20.0, 30.0, 40.0, 'Text');
    }

    public function testCreatesParametersUsingCurrentFpdfPage(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);
        $pdf->AddPage();
        $pdf->addSignatureBox('approval', 10.0, 20.0, 30.0, 40.0, 'Signed by $$SUBJECTCN$$');

        self::assertSame([
            'signaturePositionOnPageLowerLeftX' => 10,
            'signaturePositionOnPageLowerLeftY' => 240,
            'signaturePositionOnPageUpperRightX' => 40,
            'signaturePositionOnPageUpperRightY' => 280,
            'signaturePage' => 1,
            'layer2Text' => 'Signed by $$SUBJECTCN$$',
        ], $pdf->getAutoFirmaParameters('approval'));
    }

    public function testTracksSignatureBoxesAcrossPages(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);
        $pdf->AddPage();
        $pdf->addSignatureBox('first', 10.0, 10.0, 20.0, 20.0, 'First');
        $pdf->AddPage();
        $pdf->addSignatureBox('second', 10.0, 10.0, 20.0, 20.0, 'Second');

        self::assertTrue($pdf->hasSignatureBox(' first '));
        self::assertFalse($pdf->hasSignatureBox('missing'));
        self::assertSame(['first', 'second'], $pdf->getSignatureBoxNames());
        self::assertSame(1, $pdf->getAutoFirmaParameters('first')['signaturePage']);
        self::assertSame(2, $pdf->getAutoFirmaParameters('second')['signaturePage']);
        self::assertCount(2, $pdf->getAllAutoFirmaParameters());
    }

    public function testRejectsDuplicateNameAfterNormalization(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);
        $pdf->AddPage();
        $pdf->addSignatureBox('approval', 10.0, 10.0, 20.0, 20.0, 'First');

        $this->expectException(DuplicateSignatureBox::class);
        $pdf->addSignatureBox(' approval ', 40.0, 10.0, 20.0, 20.0, 'Second');
    }

    public function testRejectsUnknownName(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);
        $pdf->AddPage();

        $this->expectException(UnknownSignatureBox::class);
        $pdf->getAutoFirmaParameters('missing');
    }

    public function testRejectsBoxOutsideCurrentPage(): void
    {
        $pdf = new FpdfAutoFirma('P', 'pt', [200, 300]);
        $pdf->AddPage();

        $this->expectException(InvalidSignatureBox::class);
        $pdf->addSignatureBox('outside', 190.0, 10.0, 20.0, 20.0, 'Text');
    }
}
