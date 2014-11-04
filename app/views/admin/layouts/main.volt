<!DOCTYPE html>
<html>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport' />
        {{ tag.getTitle() }}
        <meta content='text/html; charset=UTF-8' http-equiv='Content-Type' />
        
        <!-- Bootstrap CSS Admin-->
        <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}public/plugins/bootstrap/css/bootstrap.min.css">
        <!-- FortAwesome -->
        <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}public/plugins/FortAwesome/css/font-awesome.min.css">
        
        <!-- Custom CSS Admin -->
        <link rel="stylesheet" type="text/css" href="{{ config.application.baseUri }}min/index.php?g=cssAdmin&rev={{ setting.global.cssAdminRev }}">
        
        <!-- Jquery -->
        <script src="{{ config.application.baseUri }}public/js/jquery.min.js"></script>
        <!-- Bootstrap Admin-->
        <script type="text/javascript" src="{{ config.application.baseUri }}public/plugins/bootstrap/js/bootstrap.min.js"></script>

        <script type="text/javascript">
            var rooturl = "{{ config.application.baseUri }}";
            var rooturl_admin = "{{ config.application.baseUriAdmin }}";
        </script>
    </head>
    
    <body>
   
    {% include "header.volt" %}

    {% block content %}

    {% endblock %}

    {% include "footer.volt" %}

    </body>

</html>