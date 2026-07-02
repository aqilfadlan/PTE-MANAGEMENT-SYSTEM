<?php

function console_log(mixed ...$values): void
{
    if (($_ENV['APP_DEBUG'] ?? 'false') !== 'true') {
        return;
    }

    foreach ($values as $value) {
        $json = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        echo '<script>console.log(' . $json . ');</script>' . "\n";
    }
}
