<?php

session_start();

// Destroy the session
session_destroy();

// Redirect to landing page
header("Location: ../../index.php");
exit();
?>