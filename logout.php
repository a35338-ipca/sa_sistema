<?php
require_once __DIR__ . '/includes/auth.php';
doLogout();
header('Location: '.SITE_URL.'/academicologin.php?logout=1');
exit;
