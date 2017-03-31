<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Signin Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
    <style type="text/css">
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

    </style>
  </head>

  <body>

    <div class="container">

      <form class="form-signin" action="craw.php" method="post">
        <h2 class="form-signin-heading">Pega Mulek do SIGE</h2>
        <h6 class="form-signin-heading">A crawler for Sige</h6>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="input" id="inputEmail" name="login" class="form-control" placeholder="Login" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="pass" class="form-control" placeholder="Senha" required>
        <label for="inano" class="sr-only">Ano</label>
        <input type="text" id="inano" name="ano" class="form-control" placeholder="ano" required value='2016'>
        <div class="checkbox">
          <select name="cidade" class="form-control">
<?php
include('cidades.php');
?>
          </select>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button>
      </form>

    </div> <!-- /container -->

  </body>
</html>
