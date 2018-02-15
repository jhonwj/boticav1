<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../resources/font-awesome/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="../resources/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../resources/css/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="../resources/css/main.css">
<script src="../resources/js/jquery-3.2.1.min.js"></script>
<script src="../resources/js/main.js"></script>
<script src="../resources/js/bootstrap.js"></script>
<script src="../resources/js/jquery.dataTables.js"></script>
<script src="../resources/js/dataTables.bootstrap.js"></script>
<script src="../resources/js/bootstrap-notify.js"></script>
<script src="../resources/js/jquery.print.js"></script>
<script src="../resources/js/jspdf.min.js"></script>
<script src="../resources/js/jspdf.plugin.autotable.js"></script>
<script src="../resources/js/FileSaver.js"></script>


<?php
 session_start();
if (!isset($_SESSION['user'])) {
  ?>
  <script>
    window.location.href = "/"
  </script>
  <?php
    //header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
    exit();
}
?>
