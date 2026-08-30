<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma;

use Erseco\FpdfAutoFirma\Exception\InvalidSignatureBox;

/**
 * Signature area expressed using FPDF coordinates and units.
 */
final class SignatureBox
{
    /** @var string */
    private $name;

    /** @var float */
    private $x;

    /** @var float */
    private $y;

    /** @var float */
    private $width;

    /** @var float */
    private $height;

    /** @var string */
    private $text;

    public function __construct(
        string $name,
        float $x,
        float $y,
        float $width,
        float $height,
        string $text
    ) {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidSignatureBox('Signature box name cannot be empty.');
        }

        if (trim($text) === '') {
            throw new InvalidSignatureBox('Visible signature text cannot be empty.');
        }

        $this->assertFinite($x, 'x');
        $this->assertFinite($y, 'y');
        $this->assertFinite($width, 'width');
        $this->assertFinite($height, 'height');

        if ($x < 0 || $y < 0) {
            throw new InvalidSignatureBox('Signature box position cannot be negative.');
        }

        if ($width <= 0 || $height <= 0) {
            throw new InvalidSignatureBox('Signature box dimensions must be greater than zero.');
        }

        $this->name = $name;
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        $this->text = $text;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function width(): float
    {
        return $this->width;
    }

    public function height(): float
    {
        return $this->height;
    }

    public function text(): string
    {
        return $this->text;
    }

    /**
     * Ensures that the box is fully contained in the current FPDF page.
     */
    public function assertFits(float $pageWidth, float $pageHeight): void
    {
        $this->assertFinite($pageWidth, 'page width');
        $this->assertFinite($pageHeight, 'page height');

        if ($pageWidth <= 0 || $pageHeight <= 0) {
            throw new InvalidSignatureBox('Page dimensions must be greater than zero.');
        }

        if ($this->x + $this->width > $pageWidth || $this->y + $this->height > $pageHeight) {
            throw new InvalidSignatureBox('Signature box must fit entirely inside the current page.');
        }
    }

    private function assertFinite(float $value, string $field): void
    {
        if (!is_finite($value)) {
            throw new InvalidSignatureBox(sprintf('Signature box %s must be finite.', $field));
        }
    }
}
