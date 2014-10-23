
{% block content %}
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Pacifico">
    <style type="text/css">

      html {
        height: 100%;
        margin-bottom: 1px;
      }

      body {
        background-color: #2c3e50;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: normal;
        height: 100%;
        overflow: hidden;
        margin: 0;
        padding: 0;
      }

      .error {
        margin-left: auto;
        margin-right: auto;
      }

      #outline {
        background: url(public/images/error/fnf-bg.png) no-repeat left top;
        height: 312px;
        margin: 100px auto 0;
        overflow: hidden;
        position: relative;
        width: 653px;
      }

      #errorboxoutline { margin: 0; padding: 0; }

      #error-code {
        color: #FFF;
        font-family: 'Pacifico', cursive, sans-serif;
        font-size: 500%;
        left: 180px;
        line-height: 1;
        position: absolute;
        text-shadow: 1px 1px 0 rgba(0,0,0,.3);
        top: 20px;
      }

      #error-message {
        color: #FFF;
        margin: 105px 170px 0;
        overflow: hidden;
      }
    </style>


    <div class="error">
        <div id="outline">
            <div id="errorboxoutline">
                <div id="error-code">404</div>
                <div id="error-message">Page not found</div>
            </div>
        </div>
    </div>

{% endblock %}