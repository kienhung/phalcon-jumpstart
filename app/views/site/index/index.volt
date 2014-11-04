{% extends "layouts/main.volt" %}

{% block content %}

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <!-- <li><a href="http://www.guidgenerator.com/online-guid-generator.aspx" target="_blank">Unique ID</a></li>
          <li><a href="http://randomkeygen.com/" target="_blank">Hash Key</a></li> -->
          <li class="active"><a href="https://github.com/nguyenducduy/phalcon-jumpstart" target="_blank">View on Github</a></li>
        </ul>
        <h3 class="text-muted">Phalcon Jumpstart</h3>
      </div>

      <div class="jumbotron">
        <h1>Begin Jumping</h1>
        <p class="lead">
            You see me, it's ok because this is default & sample controller.
        </p>
        <p><a class="btn btn-lg btn-success" href="{{ config.application.baseUri }}install" role="button">Installation</a></p>
      </div>

      <div class="footer">
        <p>Copyright 2014 &copy; Phalcon Jumpstart</p>
      </div>

    </div> <!-- /container -->

{% endblock %}