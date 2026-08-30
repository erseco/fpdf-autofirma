<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Tests\Unit;

use Erseco\FpdfAutoFirma\CoordinateConverter;
use Erseco\FpdfAutoFirma\SignatureBox;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CoordinateConverterTest extends TestCase
{
    public function testConvertsTopLeftOriginToBottomLeftOrigin(): void
    {
        $box = new SignatureBox('box', 10.0, 20.0, 30.0, 40.0, 'Text');
        $rectangle = (new CoordinateConverter())->convert($box, 100.0, 1.0);

        self::assertSame(10, $rectangle->lowerLeftX());
        self::assertSame(40, $rectangle->lowerLeftY());
        self::assertSame(40, $rectangle->upperRightX());
        self::assertSame(80, $rectangle->upperRightY());
    }

    public function testAppliesFpdfUnitScaleAndRoundsToPoints(): void
    {
        $box = new SignatureBox('box', 1.25, 2.25, 3.5, 4.5, 'Text');
        $rectangle = (new CoordinateConverter())->convert($box, 20.0, 2.0);

        self::assertSame(3, $rectangle->lowerLeftX());
        self::assertSame(27, $rectangle->lowerLeftY());
        self::assertSame(10, $rectangle->upperRightX());
        self::assertSame(36, $rectangle->upperRightY());
    }

    /**
     * @dataProvider invalidMetricsProvider
     */
    public function testRejectsInvalidPageMetrics(float $pageHeight, float $scale): void
    {
        $box = new SignatureBox('box', 0.0, 0.0, 10.0, 10.0, 'Text');

        $this->expectException(InvalidArgumentException::class);
        (new CoordinateConverter())->convert($box, $pageHeight, $scale);
    }

    /**
     * @return array<string, array{float, float}>
     */
    public function invalidMetricsProvider(): array
    {
        return [
            'zero page height' => [0.0, 1.0],
            'infinite page height' => [INF, 1.0],
            'zero scale' => [100.0, 0.0],
            'nan scale' => [100.0, NAN],
        ];
    }
}
