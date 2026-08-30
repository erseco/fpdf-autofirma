<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma;

use InvalidArgumentException;

/**
 * Converts FPDF top-left coordinates to PDF bottom-left coordinates.
 */
final class CoordinateConverter
{
    public function convert(SignatureBox $box, float $pageHeight, float $pointsPerUnit): PdfRectangle
    {
        if (!is_finite($pageHeight) || $pageHeight <= 0) {
            throw new InvalidArgumentException('Page height must be a positive finite number.');
        }

        if (!is_finite($pointsPerUnit) || $pointsPerUnit <= 0) {
            throw new InvalidArgumentException('FPDF scale must be a positive finite number.');
        }

        $lowerLeftX = $this->round($box->x() * $pointsPerUnit);
        $lowerLeftY = $this->round(($pageHeight - $box->y() - $box->height()) * $pointsPerUnit);
        $upperRightX = $this->round(($box->x() + $box->width()) * $pointsPerUnit);
        $upperRightY = $this->round(($pageHeight - $box->y()) * $pointsPerUnit);

        return new PdfRectangle($lowerLeftX, $lowerLeftY, $upperRightX, $upperRightY);
    }

    private function round(float $value): int
    {
        return (int) round($value, 0, PHP_ROUND_HALF_UP);
    }
}
