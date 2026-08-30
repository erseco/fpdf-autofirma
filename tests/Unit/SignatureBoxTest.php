<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Tests\Unit;

use Erseco\FpdfAutoFirma\Exception\InvalidSignatureBox;
use Erseco\FpdfAutoFirma\SignatureBox;
use PHPUnit\Framework\TestCase;

final class SignatureBoxTest extends TestCase
{
    public function testStoresValidatedValues(): void
    {
        $box = new SignatureBox(' approval ', 10.0, 20.0, 30.0, 40.0, 'Signed by $$SUBJECTCN$$');

        self::assertSame('approval', $box->name());
        self::assertSame(10.0, $box->x());
        self::assertSame(20.0, $box->y());
        self::assertSame(30.0, $box->width());
        self::assertSame(40.0, $box->height());
        self::assertSame('Signed by $$SUBJECTCN$$', $box->text());
    }

    /**
     * @dataProvider invalidDefinitionsProvider
     */
    public function testRejectsInvalidDefinitions(
        string $name,
        float $x,
        float $y,
        float $width,
        float $height,
        string $text
    ): void {
        $this->expectException(InvalidSignatureBox::class);

        new SignatureBox($name, $x, $y, $width, $height, $text);
    }

    /**
     * @return array<string, array{string, float, float, float, float, string}>
     */
    public function invalidDefinitionsProvider(): array
    {
        return [
            'empty name' => ['', 0.0, 0.0, 10.0, 10.0, 'Text'],
            'empty text' => ['box', 0.0, 0.0, 10.0, 10.0, '   '],
            'negative x' => ['box', -1.0, 0.0, 10.0, 10.0, 'Text'],
            'negative y' => ['box', 0.0, -1.0, 10.0, 10.0, 'Text'],
            'zero width' => ['box', 0.0, 0.0, 0.0, 10.0, 'Text'],
            'negative height' => ['box', 0.0, 0.0, 10.0, -1.0, 'Text'],
            'infinite x' => ['box', INF, 0.0, 10.0, 10.0, 'Text'],
            'nan height' => ['box', 0.0, 0.0, 10.0, NAN, 'Text'],
        ];
    }

    public function testAcceptsBoxExactlyAtPageBoundary(): void
    {
        $box = new SignatureBox('box', 10.0, 20.0, 90.0, 80.0, 'Text');

        $box->assertFits(100.0, 100.0);

        self::addToAssertionCount(1);
    }

    public function testRejectsBoxOutsidePage(): void
    {
        $box = new SignatureBox('box', 90.0, 20.0, 20.0, 20.0, 'Text');

        $this->expectException(InvalidSignatureBox::class);
        $box->assertFits(100.0, 100.0);
    }

    /**
     * @dataProvider invalidPageDimensionsProvider
     */
    public function testRejectsInvalidPageDimensions(float $width, float $height): void
    {
        $box = new SignatureBox('box', 0.0, 0.0, 10.0, 10.0, 'Text');

        $this->expectException(InvalidSignatureBox::class);
        $box->assertFits($width, $height);
    }

    /**
     * @return array<string, array{float, float}>
     */
    public function invalidPageDimensionsProvider(): array
    {
        return [
            'zero width' => [0.0, 100.0],
            'zero height' => [100.0, 0.0],
            'infinite width' => [INF, 100.0],
        ];
    }
}
