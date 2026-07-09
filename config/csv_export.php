<?php

/**
 * Streams $rows as a downloadable CSV and exits. $rows is a list of
 * associative arrays (e.g. straight from oci_fetch_assoc); $headers maps
 * each column's display label to the array key it reads from.
 */
function exportCsv(string $filename, array $headers, array $rows): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel renders non-ASCII names correctly

    fputcsv($out, array_keys($headers));
    foreach ($rows as $row) {
        $line = [];
        foreach ($headers as $key) {
            $line[] = $row[$key] ?? '';
        }
        fputcsv($out, $line);
    }

    fclose($out);
    exit;
}
