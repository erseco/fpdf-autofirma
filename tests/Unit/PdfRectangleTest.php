<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Tests\Unit;

use Erseco\FpdfAutoFirma\PdfRectangle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PdfRectangleTest extends TestCase
{
    public function testExposesCoordinates(): void
    {
        $rectangle = new PdfRectangle(10, 20, 30, 40);

        self::assertSame(10, $rectangle->lowerLeftX());
        self::assertSame(20, $rectangle->lowerLeftY());
        self::assertSame(30, $rectangle->upperRightX());
        self::assertSame(40, $rectangle->upperRightY());
    }

    /**
     * @dataProvider invalidRectanglesProvider
     */
    public function testRejectsInvalidRectangles(int $llx, int $lly, int $urx, int $ury): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PdfRectangle($llx, $lly, $urx, $ury);
    }

    /**
     * @return array<string, array{int, int, int, int}>
     */
    public function invalidRectanglesProvider(): array
    {
        return [
            'negative x' => [-1, 0, 10, 10],
            'negative y' => [0, -1, 10, 10],
            'zero width' => [10, 0, 10, 10],
            'negative height' => [0, 10, 10, 9],
        ];
    }
}
