<?php include_once('info.php');?>
<html class>
<head>
  <title><?php echo NOMBRE_SISTEMA ?></title>
 <link rel="manifest" href="/manifest.json">
 <script src="resources/js/jquery-3.2.1.min.js"></script>

</head>
<style type="text/css">
html,
body {
  width: 100%;
  height: 100%;
}
body {
  font-family: 'Roboto', sans-serif;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
  margin: 0;
}
body:before {
  content: "";
  width: 100%;
  height: 100%;
  display: block;
  background: url('/resources/images/fondo.jpg') no-repeat;
  background-size: cover;
  position: absolute;
}
.login-box {
  position: relative;
  top: 5%;
  left: 50%;
  width: 320px;
  margin: 0 auto;
  margin-left: -160px;
  background: rgba(255,255,255,0.8);
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0,0,0,0.15);
  overflow: hidden;
  transition: 0.5s all;
}
.login-box:hover {
  transform: scale(1.02);
  transition: 0.5s all;
  box-shadow: 0 20px 80px rgba(0,0,0,0.3);
}
.login-box * {
  box-sizing: border-box;
}
.login-header {
  background: #e74c3c00;
  padding: 30px;
  text-align: center;
  color: #c0392b;
  text-shadow: 0 2px 2px rgba(0,0,0,0.2);
  box-shadow: inset 0 -1px rgba(255,255,255,0.8), 0 4px rgba(0,0,0,0.06), inset 0 4px rgba(255,255,255,0.2), inset 0 2px 2px rgba(255,255,255,0.2);
}
.login-header h1 {
  font-size: 2em;
  margin-bottom: 0.5em;
}
.login-icon {
  width: 50px;
  fill: #c0392b;
  -webkit-filter: drop-shadow(0 2px 2px rgba(0,0,0,0.2));
  filter: drop-shadow(0 2px 2px rgba(0,0,0,0.2));
}
.login-sign {
  text-decoration: none;
  color: #a8ff67;
}
.login-content {
  padding: 20px;
}
.login-content input,
.login-content button,
.login-content a {
  width: 100%;
  display: inline-block;
}
.login-mail,
.login-pass {
  margin-bottom: 20px;
  padding: 10px;
  color: #999;
  border: 1px solid transparent;
  border-radius: 5px;
  outline: none;
  transition: 0.5s all;
}
.login-mail:hover,
.login-pass:hover,
.login-mail:focus,
.login-pass:focus {
  transition: 0.5s all;
  box-shadow: 0 2px rgba(153,153,153,0.2);
  border: 1px solid rgba(153,153,153,0.4);
}
.login-forgot {
  margin-bottom: 20px;
  text-align: right;
  text-decoration: none;
  color: #8996a4;
  font-size: 0.8em;
  transition: 0.2s all;
}
.login-forgot:hover {
  transition: 0.2s all;
  color: #009fd7;
}
.login-enter {
  padding: 14px;
  text-transform: uppercase;
  border-radius: 5px;
  border: none;
  background: #76d035;
  color: #fff;
  outline: none;
  cursor: pointer;
  text-shadow: 0 2px 1px rgba(0,0,0,0.2);
  transition: 0.5s all;
}
.login-enter:hover {
  background: #94ee53;
  background-image: linear-gradient(to bottom, rgba(255,255,255,0), #76d035);
  transform: scale(1.04);
  transition: 0.5s all;
}
.login-enter:active {
  background: #58b217;
  background-image: linear-gradient(to top, rgba(255,255,255,0), #449e03);
  transform: scale(0.98);
  transition: 0.3s all;
}
</style>
<script type="text/javascript">
  $(document).ready(function(){
    $("#frmLogin").submit(function(e){
      var xhr = $.ajax({
        url: "../controllers/validarLogin.php",
        type: "post",
        data: {nameUser: $('#nameUser').val(), namePass: $('#namePass').val()},
        dataType: "html",
        success: function(res){
            res = $.parseJSON(res);
            if (res.isLogin) {
              window.location = "/views/V_VentaForm.php";
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("Status: " + textStatus); alert("Error: " + errorThrown);
        }
      });
      e.preventDefault();
    });
  });

</script>
<body>
<section class="login-box">
  <header class="login-header">
    <img src="/resources/images/logo.png" style="max-width: 50%" />
    <!-- <h1></h1> -->
    <h3 style="margin-bottom: -15px;">Ingrese sus datos</h3>
  </header>
  <div class="login-content">
    <form id="frmLogin" method="post">
    <input class="login-mail" name="nameUser" id="nameUser" type="text" placeholder="Usuario"/>
    <input class="login-pass" name="namePass" id="namePass" type="password" placeholder="Contraseña"/>
    <button type="submit" class="login-enter">Entrar    </button>
    </form>
    <div style="text-align: center; font-weight: bold;">
      <label>CONTACTO</label>
      <br>
      <br>
      <label>Telf: (062) 511550 -  Cel: 954370221</label>
      <br>
      <br>
      <label>Cel. Soporte: 997578199</label>
      <br>
      <br>
      <label><a href="http://neurosystemperu.com" target="_blank">Neuro System Perú</a></label>
    </div>
  </div>
</section>
</body>
</html>
