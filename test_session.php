<?php
session_start();
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 1;
} else {
    $_SESSION['test']++;
}
echo "Oturum testi: " . $_SESSION['test'];
?>