{% extends "layouts/main.volt" %}

{% block content %}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_profile">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-user"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}profile" style="color:#333;">{{ lang._('IndexTitle') }}</a> / {{ lang._('ButtonChangePassword') }}
                </h1>

                <div class="header-right">
                    <a href="{{ config.application.baseUriAdmin }}profile" class="btn btn-success">{{ lang._('ButtonYourProfile') }}</a>
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
                        
                    </div>
                    <div class="col-md-9">
                
                        <div class="col-md-6 ssb clear inner-left">
                            <label for="foldpassword">{{ lang._('LabelOldPassword') }} <span class="star_require">*</span></label>
                            <input type="password" name="foldpassword" id="foldpassword" value=""/>
                        </div>

                        <div class="col-md-6 ssb clear inner-left">
                            <label for="fnewpassword">{{ lang._('LabelNewPassword') }} <span class="star_require">*</span></label>
                            <input type="password" name="fnewpassword" id="fnewpassword" value=""/>
                        </div>

                        <div class="col-md-6 ssb clear inner-left">
                            <label for="fconfirmpassword">{{ lang._('LabelConfirmPassword') }} <span class="star_require">*</span></label>
                            <input type="password" name="fconfirmpassword" id="fconfirmpassword" value=""/>
                        </div>

                    </div>
                </div>

                <div class="row section buttons">
                    <input type="submit" name="fsubmit" value="{{ lang._('UpdateButton') }}" class="btn btn-success">
                    <a href="{{ config.application.baseUriAdmin }}profile" class="btn btn-default">{{ lang._('CancelButton') }}</a>
                    <span class="pull-left"><span class="star_require">*</span> : {{ lang._('Required') }}</span>
                </div>
            </form>
        </div>
    </div>

{% endblock %}
