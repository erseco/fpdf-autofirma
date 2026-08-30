<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Exception;

use OutOfBoundsException;

/**
 * Thrown when a requested signature box does not exist.
 */
final class UnknownSignatureBox extends OutOfBoundsException
{
}
