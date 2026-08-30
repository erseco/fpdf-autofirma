<?php

declare(strict_types=1);

namespace Erseco\FpdfAutoFirma\Exception;

use LogicException;

/**
 * Thrown when a signature box name is already registered.
 */
final class DuplicateSignatureBox extends LogicException
{
}
