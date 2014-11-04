{% extends "layouts/main.volt" %}

{% block content %}

    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_{{MODULE_LOWER}}_edit">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa {{FA_ICON}}"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}{{MODULE_LOWER}}" style="color:#333;">{{MODULE}}</a> / {{ lang._('EditTitle') }}
                </h1>
            </header>
        </div>
    </div>

    {{ content() }}

    <form method="post" action="" role="form" enctype="multipart/form-data">
        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
        <div class="row section">

            <div class="col-md-3 section-summary">
                <h2>{{ lang._('OverViewTitle') }}</h2>
                <p>{{ lang._('OverView') }}</p>
            </div>

            <div class="col-md-9">
                {{FORM_EDIT_CONTROLGROUP}}
            </div>
        </div>
        <div class="row section buttons">
            <input type="submit" name="fsubmit" value="{{ lang._('EditButton') }}" class="btn btn-success">
            <a href="{{ config.application.baseUriAdmin }}{{MODULE_LOWER}}" class="btn btn-default">{{ lang._('CancelButton') }}</a>
            <span class="pull-left"><span class="star_require">*</span> : {{ lang._('Required') }}</span>
        </div>
    </form>

{% endblock %}
