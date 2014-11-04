<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    {{ getTitle() }}
    <!-- Bootstrap CSS Admin-->
    <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}public/plugins/bootstrap/css/bootstrap.min.css">
    <!-- FortAwesome -->
    <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}public/plugins/FortAwesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}min/index.php?g=cssAdmin&rev={{ setting.global.cssAdminRev }}">
this.
  </head>

  <body class="login_body">

    <div class="wrap">
        {{ content() }}
        <form class="form-signin" role="form" method="post" action="">
          <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}"/>
          <h2 class="form-signin-heading">Authentication</h2>
          <input type="email" name="femail" value="" placeholder="Email address" class="form-control login-field" required autofocus>
          <input type="password" name="fpassword" placeholder="Password" class="form-control login-field" required>
          <label class="checkbox">
            <input type="checkbox" name="fcookie" value="remember-me"> Remember me
          </label>
          <input type="submit" name="fsubmit" value="LOGIN" class="btn btn-lg btn-primary btn-block" />
        </form>

    </div> <!-- /container -->

  </body>
</html>
