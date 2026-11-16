<?php
/**
 * src/auth/logout.php
 * Destroys session and redirects to auth page.
 */

declare(strict_types=1);

session_start();
session_unset();
session_destroy();

header('Location: /pages/auth.html');
exit();
