<?php
session_start();

echo "Cerrando sesión ";
session_destroy();   // function that Destroys Session
header("Location: /");
?>
