<?php

function console_log($data) {
    $output = json_encode($data);
    echo "<script>console.log({$output});</script>";
}
?>
