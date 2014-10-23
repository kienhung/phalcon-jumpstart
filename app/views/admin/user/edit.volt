{% extends "layouts/main.volt" %}

{% block content %}

    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_user">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-users"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}user" style="color:#333;">Users</a> / Edit
                </h1>
            </header>
        </div>
    </div>

    {{ content() }}

    <form method="post" action="" role="form" enctype="multipart/form-data">
        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
        <div class="row section">

            <div class="col-md-3 section-summary">
                <h2>User Edit</h2>
                <p>Edit Information of selected User</p>
                <img src="{{ myUser.getMediumImage() }}" alt="{{ myUser.name }}" class="img-thumbnail" style="border-radius: 50%; max-width: 80%;"/>
            </div>

            <div class="col-md-9">
                <div class="col-md-6 ssb clear inner-left">
                    <label for="fname">Name <span class="star_require">*</span></label>
                    <input type="text" name="fname" id="fname" value="{% if formData['fname'] is defined %}{{ formData['fname']|escape }}{% endif %}">
                </div>
                <div class="col-md-6 ssb clear inner-left">
                    <label for="femail">Email <span class="star_require">*</span></label>
                    <input type="text" name="femail" id="femail" value="{% if formData['femail'] is defined %}{{ formData['femail']|escape }}{% endif %}" disabled>
                </div>
                <div class="col-md-6 ssb clear inner-left">
                    <label for="frole">Role <span class="star_require">*</span></label>
                    <select id="frole" name="frole" class="col-md-12">
                        <option value="0">- - - -</option>
                        {% for key, role in roleList %}
                            <option value="{{ key }}" {% if formData['frole'] is defined and formData['frole'] == key %}selected="selected"{% endif %}>{{ role }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="col-md-6 ssb clear inner-left">
                    <label for="fstatus">Status <span class="star_require">*</span></label>
                    <select id="fstatus" name="fstatus" class="col-md-12">
                        <option value="0">- - - -</option>
                        {% for key, status in statusList %}
                            <option value="{{ key }}" {% if formData['fstatus'] is defined and formData['fstatus'] == key %}selected="selected"{% endif %}>{{ status }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="col-md-12 ssb clear inner-left">
                    <label for="favatar">Avatar <span class="star_require">*</span></label>
                    <input type="file" name="favatar" id="favatar" />
                </div>

            </div>
        </div>
        <div class="row section buttons">
            <input type="submit" name="fsubmit" value="UPDATE" class="btn btn-success">
            <a href="{{ config.application.baseUriAdmin }}user" class="btn btn-default">Cancel</a>
            <span class="pull-left"><span class="star_require">*</span> : Required</span>
        </div>
    </form>

{% endblock %}
