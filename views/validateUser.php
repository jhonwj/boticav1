<?php
session_start();

if (!isset($_SESSION['user']) && !isset($_POST['nameUser'])) {
  ?>
  <script>
    window.location.href = "/"
  </script>
  <?php
    //header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
    exit();
}
?>
