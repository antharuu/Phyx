<?php

declare(strict_types=1);

/**
 * Fails (exit 1) when the Clover coverage report does not reach 100 % on
 * every tracked metric (statements, methods, conditionals, elements).
 *
 * Invoked by `composer coverage:check` after `phpunit` has produced
 * `coverage/clover.xml`.
 */

$reportPath = $argv[1] ?? __DIR__ . '/../coverage/clover.xml';

if (!is_file($reportPath)) {
    fwrite(STDERR, "coverage-check: report not found at {$reportPath}\n");
    fwrite(STDERR, "run `composer coverage` first to produce it.\n");
    exit(2);
}

$xml = @simplexml_load_file($reportPath);
if ($xml === false) {
    fwrite(STDERR, "coverage-check: failed to parse {$reportPath}\n");
    exit(2);
}

$projectMetrics = $xml->project->metrics ?? null;
if ($projectMetrics === null) {
    fwrite(STDERR, "coverage-check: no project/metrics element in clover report\n");
    exit(2);
}

$metrics = [
    'statements'   => ['total' => 'statements',   'covered' => 'coveredstatements'],
    'methods'      => ['total' => 'methods',      'covered' => 'coveredmethods'],
    'conditionals' => ['total' => 'conditionals', 'covered' => 'coveredconditionals'],
    'elements'     => ['total' => 'elements',     'covered' => 'coveredelements'],
];

$failures = [];
foreach ($metrics as $label => $keys) {
    $total = (int) ($projectMetrics[$keys['total']] ?? 0);
    $covered = (int) ($projectMetrics[$keys['covered']] ?? 0);

    if ($total === 0) {
        continue;
    }

    if ($covered !== $total) {
        $ratio = $total > 0 ? ($covered / $total) * 100.0 : 0.0;
        $failures[] = sprintf(
            '%-13s %d / %d (%.2f%%)',
            $label . ':',
            $covered,
            $total,
            $ratio,
        );
    }
}

if ($failures !== []) {
    fwrite(STDERR, "coverage-check: below 100 % on the following metrics:\n");
    foreach ($failures as $line) {
        fwrite(STDERR, '  ' . $line . "\n");
    }
    fwrite(STDERR, "see coverage/html/index.html for the line-level report.\n");
    exit(1);
}

fwrite(STDOUT, "coverage-check: 100 % on every tracked metric.\n");
exit(0);
