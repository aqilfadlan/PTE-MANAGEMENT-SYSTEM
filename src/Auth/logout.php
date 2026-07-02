<?php

session_start();
session_destroy();

header('Location: /PTE-MANAGEMENT-SYSTEM/login');
exit;
