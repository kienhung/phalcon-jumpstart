{% extends "layouts/main.volt" %}

{% block content %}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_profile">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-user"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}profile" style="color:#333;">{{ lang._('IndexTitle') }}</a>
                </h1>

                <div class="header-right">
                    <a href="{{ config.application.baseUriAdmin }}profile/changepassword" class="btn btn-success">{{ lang._('ButtonChangePassword') }}</a>
                </div>
            </header>
        </div>
    </div>

    {{ content() }}
    {{ flashSession.output() }}

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="index-listing">
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
                <div class="row section">
                    <div class="col-md-3 section-summary">
                        <div class="profile-gravatar">
                            <img src="{{ me.getMediumImage() }}" class="ico ico-profile-gravatar">
                        </div>
                    </div>
                    <div class="col-md-9">
                
                        <div class="col-md-6 ssb clear inner-left">
                            <label for="fname">{{ lang._('LabelName') }} <span class="star_require">*</span></label>
                            <input type="text" name="fname" id="fname" value="{{ me.name|escape }}"/>
                        </div>

                        <div class="col-md-6 ssb clear inner-left">
                            <label for="femail">{{ lang._('LabelEmail') }} <span class="star_require">*</span></label>
                            <input type="text" name="femail" id="femail" value="{{ me.email|escape }}" disabled="" />
                        </div>

                        <div class="col-md-6 ssb clear inner-left">
                            <label for="avatar">{{ lang._('LabelAvatar') }} <span class="star_require">*</span></label>
                            <input type="file" name="favatar" id="favatar" value=""/>
                        </div>

                    </div>
                </div>

                <div class="row section buttons">
                    <input type="submit" name="fsubmit" value="{{ lang._('UpdateButton') }}" class="btn btn-success">
                    <span class="pull-left"><span class="star_require">*</span> : {{ lang._('Required') }}</span>
                </div>
            </form>
        </div>
    </div>

{% endblock %}
