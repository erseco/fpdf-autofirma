<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma;

use Erseco\FpdfAutoFirma\Exception\DuplicateSignatureBox;
use Erseco\FpdfAutoFirma\Exception\UnknownSignatureBox;
use LogicException;

/**
 * FPDF extension that prepares visible PAdES signature parameters for AutoFirma.
 */
class FpdfAutoFirma extends \FPDF
{
    /** @var array<string, AutoFirmaParameters> */
    private $signatureParameters = [];

    public function addSignatureBox(
        string $name,
        float $x,
        float $y,
        float $width,
        float $height,
        string $text
    ): void {
        $page = $this->PageNo();

        if (!is_int($page)) {
            throw new LogicException('FPDF returned an invalid page number.');
        }

        if ($page < 1) {
            throw new LogicException('Add an FPDF page before defining a signature box.');
        }

        $box = new SignatureBox($name, $x, $y, $width, $height, $text);
        $name = $box->name();

        if (isset($this->signatureParameters[$name])) {
            throw new DuplicateSignatureBox(sprintf('Signature box "%s" already exists.', $name));
        }

        $pageWidth = $this->fpdfNumber($this->GetPageWidth(), 'page width');
        $pageHeight = $this->fpdfNumber($this->GetPageHeight(), 'page height');
        $pointsPerUnit = $this->fpdfNumber($this->k, 'scale');

        $box->assertFits($pageWidth, $pageHeight);

        $rectangle = (new CoordinateConverter())->convert($box, $pageHeight, $pointsPerUnit);
        $this->signatureParameters[$name] = AutoFirmaParameters::fromVisibleSignature(
            $page,
            $rectangle,
            $box->text()
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function getAutoFirmaParameters(string $name): array
    {
        $name = trim($name);

        if (!isset($this->signatureParameters[$name])) {
            throw new UnknownSignatureBox(sprintf('Signature box "%s" does not exist.', $name));
        }

        return $this->signatureParameters[$name]->toArray();
    }

    public function hasSignatureBox(string $name): bool
    {
        return isset($this->signatureParameters[trim($name)]);
    }

    /**
     * @return list<string>
     */
    public function getSignatureBoxNames(): array
    {
        return array_keys($this->signatureParameters);
    }

    /**
     * @return array<string, array<string, int|string>>
     */
    public function getAllAutoFirmaParameters(): array
    {
        $parameters = [];

        foreach ($this->signatureParameters as $name => $signatureParameters) {
            $parameters[$name] = $signatureParameters->toArray();
        }

        return $parameters;
    }

    /**
     * Normalizes values returned by the untyped FPDF 1.x API.
     *
     * @param mixed $value
     */
    private function fpdfNumber($value, string $name): float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new LogicException(sprintf('FPDF returned an invalid %s.', $name));
        }

        return (float) $value;
    }
}
