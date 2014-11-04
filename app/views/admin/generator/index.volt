{% extends "layouts/main.volt" %}

{% block content %}

    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_generator">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-magic"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}generator" style="color:#333;">Code Generator</a>
                </h1>
            </header>
        </div>
    </div>

    {{ content() }}

    <!-- Nav tabs -->

    <style type="text/css">
        .tbl_button {
            display:block;
            border:1px solid #ccc;
            padding:10px;
            background-color: #ecf0f1;
            color: #333;
        }
    </style>
    <div class="row">
        {% for tblName in listTables if tblName != 'user' %}
        <div class="col-xs-6 col-sm-3">
            <a class="tbl_button" href="{{ config.application.baseUriAdmin }}generator/gentable/{{ tblName }}" style="margin-bottom:10px;"><i class="fa fa-magic"></i> &nbsp; {{ tblName }}</a> <br/>
        </div>
        {% endfor %}  

    </div>

        


{% endblock %}