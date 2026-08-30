<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma;

use InvalidArgumentException;

/**
 * AutoFirma extra parameters for a visible PAdES signature.
 */
final class AutoFirmaParameters
{
    /** @var array<string, int|string> */
    private $parameters;

    /**
     * @param array<string, int|string> $parameters
     */
    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function fromVisibleSignature(int $page, PdfRectangle $rectangle, string $text): self
    {
        if ($page < 1) {
            throw new InvalidArgumentException('Signature page must be greater than zero.');
        }

        if (trim($text) === '') {
            throw new InvalidArgumentException('Visible signature text cannot be empty.');
        }

        return new self([
            'signaturePositionOnPageLowerLeftX' => $rectangle->lowerLeftX(),
            'signaturePositionOnPageLowerLeftY' => $rectangle->lowerLeftY(),
            'signaturePositionOnPageUpperRightX' => $rectangle->upperRightX(),
            'signaturePositionOnPageUpperRightY' => $rectangle->upperRightY(),
            'signaturePage' => $page,
            'layer2Text' => $text,
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return $this->parameters;
    }
}
