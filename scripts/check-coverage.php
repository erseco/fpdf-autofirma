<?php

declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php scripts/check-coverage.php <clover.xml> <minimum-percent>\n");
    exit(2);
}

$file = $argv[1];
$minimum = (float) $argv[2];

if (!is_file($file)) {
    fwrite(STDERR, sprintf("Coverage file not found: %s\n", $file));
    exit(2);
}

$document = new DOMDocument();

if (!$document->load($file)) {
    fwrite(STDERR, sprintf("Unable to parse coverage file: %s\n", $file));
    exit(2);
}

$xpath = new DOMXPath($document);
$nodes = $xpath->query('/coverage/project/metrics');
$metrics = $nodes !== false ? $nodes->item(0) : null;

if (!$metrics instanceof DOMElement) {
    fwrite(STDERR, "Clover project metrics were not found.\n");
    exit(2);
}

$statements = (int) $metrics->getAttribute('statements');
$covered = (int) $metrics->getAttribute('coveredstatements');
$coverage = $statements === 0 ? 100.0 : ($covered / $statements) * 100;

printf("Statement coverage: %.2f%% (minimum %.2f%%)\n", $coverage, $minimum);

if ($coverage + 0.00001 < $minimum) {
    fwrite(STDERR, "Coverage threshold not met.\n");
    exit(1);
}
