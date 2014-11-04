{% extends "layouts/main.volt" %}

{% block content %}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_dashboard">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-dashboard"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}dashboard" style="color:#333;">{{ lang._('IndexTitle') }}</a>
                </h1>
            </header>
        </div>
    </div>

    {{ content() }}
    {{ flashSession.output() }}

    <div class="col-md-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="2"><h1>{{ lang._('LabelSystemInformation') }}</h1></th>
                </tr>
            </thead>
            <tr>
                <td width="200" class="td_right">{{ lang._('LabelServerIp') }} :</td>
                <td>{{ formData['fserverip'] }}</td>
            </tr>
            <tr>
                <td width="200" class="td_right">{{ lang._('LabelClientIp') }} :</td>
                <td>{{ formData['fclientip'] }}</td>
            </tr>
            <tr>
                <td class="td_right">{{ lang._('LabelServerName') }} :</td>
                <td>{{ formData['fserver'] }}</td>
            </tr>
            <tr>
                <td class="td_right">{{ lang._('LabelPhpVersion') }} :</td>
                <td>{{ formData['fphp'] }}</td>
            </tr>
            <tr>
                <td class="td_right">{{ lang._('LabelUserAgent') }} :</td>
                <td>{{ formData['fuseragent'] }}</td>
            </tr>

            <tr>
                <td class="td_right">{{ lang._('LabelServerTime') }} :</td>
                <td>{{ formData['now'] }}</td>
            </tr>
        </table>
    </div>
{% endblock %}
