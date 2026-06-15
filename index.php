<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: /src/Dashboard/index.php');
} else {
    header('Location: /src/Auth/login.php');
}
exit;
