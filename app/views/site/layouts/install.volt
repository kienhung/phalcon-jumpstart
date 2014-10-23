<html>
	<head>
		<title>Installation :: PhalconJumpstart Framework</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<style type="text/css">
			body{background:#777;font-family:Helvetica, Arial, Verdana, Sans serif;font-size:12px;}
			#wrapper{width:460px;margin: 100px auto; border-radius:8px; -webkit-border-radius:8px; background:#fff;padding:20px;}
			h1{text-align:center; font-size:28px;}
			.intro{font-size:14px; color:#7A7D7D; line-height:18px; text-align:center;}
			.btnwrapper{text-align:center;margin:50px;}
			.installbtn{border-width:0; cursor:pointer;padding:10px 30px; font-size:14px; border-radius:4px; -webkit-border-radius:4px; color:#fff; font-weight:bold;; background:#ccc; text-decoration:none;background: #4096ee;}
			.installbtn:hover{color:#000;}
			#footer{text-align:center; border-top:1px dotted #ccc;margin-top:30px;padding-top:10px; color:#ccc; font-size:11px;}
			#footer a{color:#09f;}
			#footer a:hover{color:#f90;}

			#installform{background:#f5f5f5; border-radius:4px; -webkit-border-radius:4px;width:400px; padding:20px; margin:20px auto; border:1px solid #eee;}
			h2{margin:0;padding-bottom:30px;text-align:center;}
			.fitem{clear:both; font-size:14px;padding-top:5px;}
			.fitem .label{float:left; width:130px; text-align:right;padding:8px 10px 0 0;}
			.fitem .input{float:left;}
			.fitem .tbx{padding:6px; font-size:16px;border:1px solid #ccc;width:240px;}


			/*	NOTIFY BAR	*/
			.notify-bar{padding:10px;border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px;margin-bottom:10px;}
			.notify-bar-success{background:#eaffa5;color:#6c8c00;}
			.notify-bar-error{background:#ffcfce;color:#9e3737;}
			.notify-bar-text{padding:0 20px 0 10px; line-height:1.5;}
			.notify-bar-text-sep{border-top:1px solid #eee;margin:10px 0; display:none;}
		</style>
	</head>
	<body>
		{% block content %}

    	{% endblock %}
    	<script src="{{ this.config.application.baseUri }}public/js/jquery.min.js"></script>
	</body>
</html>