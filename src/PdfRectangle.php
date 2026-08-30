<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma;

use InvalidArgumentException;

/**
 * Rectangle expressed in PDF points using a bottom-left origin.
 */
final class PdfRectangle
{
    /** @var int */
    private $lowerLeftX;

    /** @var int */
    private $lowerLeftY;

    /** @var int */
    private $upperRightX;

    /** @var int */
    private $upperRightY;

    public function __construct(int $lowerLeftX, int $lowerLeftY, int $upperRightX, int $upperRightY)
    {
        if ($lowerLeftX < 0 || $lowerLeftY < 0) {
            throw new InvalidArgumentException('PDF rectangle coordinates cannot be negative.');
        }

        if ($upperRightX <= $lowerLeftX || $upperRightY <= $lowerLeftY) {
            throw new InvalidArgumentException('PDF rectangle must have a positive width and height.');
        }

        $this->lowerLeftX = $lowerLeftX;
        $this->lowerLeftY = $lowerLeftY;
        $this->upperRightX = $upperRightX;
        $this->upperRightY = $upperRightY;
    }

    public function lowerLeftX(): int
    {
        return $this->lowerLeftX;
    }

    public function lowerLeftY(): int
    {
        return $this->lowerLeftY;
    }

    public function upperRightX(): int
    {
        return $this->upperRightX;
    }

    public function upperRightY(): int
    {
        return $this->upperRightY;
    }
}
