<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear localStorage on the client side
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
?> 