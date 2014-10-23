{% extends "layouts/main.volt" %}

{% block content %}

    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_logs_edit">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-history"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}logs" style="color:#333;">Logs</a> / {{ lang._('EditTitle') }}
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
                
                <div class="col-md-6 ssb clear inner-left">
                    <label for="name">{{ lang._('LabelName') }} </label>
                    <input type="text" name="name" id="name" value="{% if formData['name'] is defined %}{{ formData['name'] }}{% endif %}"/>
                </div>
                <div class="col-md-6 ssb clear inner-left">
                    <label for="type">{{ lang._('LabelType') }} </label>
                    <select id="type" name="type" class="col-md-12">
                        <option value="0">- - - -</option>
                        {% for key, type in typeList %}
                            <option value="{{ key }}" {% if formData['type'] is defined and formData['type'] == key %}selected="selected"{% endif %}>{{ type }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="col-md-6 ssb clear inner-left">
                    <label for="content">{{ lang._('LabelContent') }} </label>
                    <textarea class="form-control" name="content" id="content">{% if formData['content'] is defined %}{{ formData['content'] }}{% endif %}</textarea>
                </div>

            </div>
        </div>
        <div class="row section buttons">
            <input type="submit" name="fsubmit" value="{{ lang._('EditButton') }}" class="btn btn-success">
            <a href="{{ config.application.baseUriAdmin }}logs" class="btn btn-default">{{ lang._('CancelButton') }}</a>
            <span class="pull-left"><span class="star_require">*</span> : {{ lang._('Required') }}</span>
        </div>
    </form>

{% endblock %}
