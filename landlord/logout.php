<?php
// logout.php

// Destroy the user's session
session_destroy();

// Redirect the user to the login page
header('Location: login1.php');
exit;