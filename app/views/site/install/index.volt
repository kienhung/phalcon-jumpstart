{% extends "layouts/install.volt" %}

{% block content %}
	<style type="text/css">
	body {
		background-color: #34495e;
	}
	</style>
	<div id="wrapper" style="background-color: #edeff1;">
		<h1>INSTALLATION</h1>
		<p class="intro">There is no user table &amp; no administrator account. <br />Press Install button to start creating tables &amp; first account.</p>


		<table width="70%" style="margin:auto; font-size:12px; background:#eee;" cellpadding="5">
			<tr><td colspan="2"><p style="text-align:center;">Current Database Config (<code>/app/config/config.php</code>)</p></td></tr>
			<tr><td>Database Server: </td><td><code>{{ config.database.host }}</code></td></tr>
			<tr><td>Database Username: </td><td><code>{{ config.database.username }}</code></td></tr>
			<tr><td>Database Password: </td><td><code>{{ config.database.password }}</code></td></tr>
			<tr><td>Database Name: </td><td><code>{{ config.database.dbname }}</code></td></tr>
		</table>

		{% if formData['fsubmit'] is defined %}
		<div class="btnwrapper"><a href="javascript:void(0)" onclick="$('#installform').toggle();$(this).hide();" class="installbtn" title="Click here to start installation">START!</a></div>
		{% endif %}

		<form action="" method="post">
		<div id="installform" {% if formData['fsubmit'] is defined %}style="display:none;"{% endif %}>
			{{ content() }}
    		{{ flashSession.output() }}

			{% if success is defined and success|length > 0 %}
				<div class="notify-bar notify-bar-success">
					<div class="notify-bar-text">
						<p>{success[0]}</p>
					</div>
				</div>

				<div class="notify-bar notify-bar-warning">
					<div class="notify-bar-text">
						<p>For Security, please REMOVE Controller Install (/Controller/Site/InstallController.php)</p>
					</div>
				</div>

			{% else %}
				<div class="fitem">
					<div class="label">Name</div>
					<div class="input"><input type="text" class="tbx" name="fname" value="{% if formData['fname'] is defined %}{{ formData['fname'] }}{% endif %}" /></div>
				</div>
				<div class="fitem">
					<div class="label">Email</div>
					<div class="input"><input type="text" class="tbx" name="femail" value="{% if formData['femail'] is defined %}{{ formData['femail']}}{% endif %}" /></div>
				</div>
				<div class="fitem">
					<div class="label">Password</div>
					<div class="input"><input type="password" class="tbx" name="fpassword" value="" /></div>
				</div>
				<div class="fitem">
					<div class="label">Retype Password</div>
					<div class="input"><input type="password" class="tbx" name="fpassword2" value="" /></div>
				</div>
				<div class="fitem">
					<div class="label">&nbsp;</div>
					<div class="input"><input type="submit" name="fsubmit" value="INSTALL" class="installbtn" /></div>
				</div>
			{% endif %}

			<div style="clear:both;"></div>
		</div><!-- end #installform -->
		</form>
		<div id="footer">
			Copyright 2014 &copy; <a href="https://github.com/nguyenducduy/phalcon-jumpstart" title="Go to JumpStart Github page" target="_blank">https://github.com/nguyenducduy/phalcon-jumpstart</a>. All rights reserved.
		</div>
	</div><!-- end #wrapper -->
{% endblock %}