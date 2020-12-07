<html class>
<head>
  <title>NEUROSOFT - </title>
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
  background: url("http://arttechnologypsyche.com/wp-content/uploads/2015/04/abstract-background.jpg") no-repeat center center fixed;
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
  background: url('/resources/images/mini.jpg') no-repeat;
  background-size: cover;
  position: absolute;
}
.login-box {
  position: relative;
  top: 10%;
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
    <svg class="login-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 612 612">
      <path d="M612 306.036C612 137.406 474.595 0 305.964 0S0 137.405 0 306.036c0 92.88 42.14 176.437 107.698 232.6.795.794 1.59 1.59 3.108 2.312C163.86 585.473 231.804 612 306.76 612c73.364 0 141.308-26.527 194.362-69.462 3.108-.795 5.493-3.108 7.01-5.493C571.453 480.088 612 398.122 612 306.035zm-583.883 0c0-153.018 124.9-277.92 277.92-277.92s277.918 124.902 277.918 277.92c0 74.955-29.635 142.826-78.063 192.845-7.806-36.718-31.225-99.168-103.072-139.717 16.408-20.31 25.732-46.838 25.732-74.955 0-67.15-54.644-121.793-121.793-121.793S184.965 217.06 184.965 284.208c0 28.117 10.12 53.85 25.732 74.955-72.497 40.55-95.916 103-102.928 139.718-49.223-49.222-79.653-117.89-79.653-192.844zM212.36 284.93c0-51.536 42.14-93.676 93.676-93.676s93.676 42.14 93.676 93.676-42.14 93.676-93.676 93.676-93.676-42.14-93.676-93.676zm-79.653 238.093c1.59-22.624 14.022-99.17 98.374-142.104 21.107 16.407 46.84 25.73 74.956 25.73 28.117 0 54.644-10.118 75.75-26.526 83.556 42.935 96.784 117.89 99.17 142.104-47.634 38.237-108.494 61.655-174.053 61.655-66.425.072-126.563-22.552-174.196-60.86z"/>
    </svg>
    <h1>SISTEMA NEUROSOFT</h1>
    <h2>Ingrese sus datos</h2>
  </header>
  <div class="login-content">
    <form id="frmLogin" method="post">
    <input class="login-mail" name="nameUser" id="nameUser" type="text" placeholder="Usuario"/>
    <input class="login-pass" name="namePass" id="namePass" type="password" placeholder="Contraseña"/>
    <button type="submit" class="login-enter">Entrar    </button>
    </form>
  </div>
</section>
</body>
</html>
