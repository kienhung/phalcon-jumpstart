{% extends "layouts/main.volt" %}

{% block content %}
    {# {{ lang._('hello') }} #}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_user">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-users"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}user" style="color:#333;">{{ lang._('LabelIndex') }}</a> (<span class="delete-decrease-count">{% if page.total_items > 0 %}{{ page.total_items }}{% else %}0{% endif %}</span>)
                </h1>

                <div class="header-right">
                    <div class="formfilterpaginatorwrapper pull-right">
                        {% if page.items is defined and page.total_pages > 1 %}
                            <!-- start paginator -->
                                {% include "layouts/pagination.volt" %}
                           <!-- end paginator -->
                       {% endif %}
                    </div>
                </div>
            </header>
        </div>
    </div>

    {{ content() }}
    {{ flashSession.output() }}

    <!-- Nav tabs -->

    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#index-listing" role="tab" data-toggle="tab">{{ lang._('LabelListing') }}</a></li>
        <li style="width:80%; margin-left:2px; margin-top:4px;" class="pull-right">
            <div class="row" style="margin:0">
                <a class="btn btn-sm btn-success pull-right" href="{{ this.config.application.baseUriAdmin }}user/add"><i class="fa fa-plus"></i> &nbsp; {{ lang._('LabelAddButton') }}</a>
                <!-- Search field -->
                <div class="col-md-4 col-xs-6 pull-right">
                    <div class="input-group">
                        <input type="text" id="keyword" class="form-control input-sm" placeholder="Search in Name, Email " value="{% if formData['keyword'] is defined %}{{ formData['keyword'] }}{% endif %}" style="font-size:13px;"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default input-sm" onclick="gosearch()" /><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="index-listing">
        <form method="post" action="" onsubmit="return confirm('{{ lang._('LabelConfirm') }} ?');">
            <input type="hidden" name="{{ this.security.getTokenKey() }}" value="{{ this.security.getToken() }}" />
            <table class="table table-hover" style="margin-top:10px">
            <thead>
                <tr>
                    <th width="3%"><input type="checkbox" class="check-all"/></th>
                    <th width="7%">
                        <a href="{{ this.config.application.baseUriAdmin }}user?sortby=id&sorttype={% if formData['sorttype']|upper == 'DESC'%}ASC{% else %}DESC{% endif %}{% if formData['keyword'] is defined %}&keyword={{ formData['keyword'] }}{% endif %}">ID</a>
                    </th>
                    <th width="55%">{{ lang._('LabelFieldName') }}</th>
                    <th width="10%">{{ lang._('LabelFieldRole') }}</th>
                    <th width="10%">{{ lang._('LabelFieldStatus') }}</th>
                    <th width="10%">{{ lang._('LabelFieldDatecreated') }}</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="9">

                        <div class="bulk-actions align-left">
                            <select name="fbulkaction">
                                <option value="">---- {{ lang._('LabelSelectOption') }} ----</option>
                                <option value="delete">{{ lang._('LabelDelete') }}</option>
                            </select>
                            <input type="submit" name="fsubmitbulk" class="btn btn-default btn-sm" value="Submit" />
                        </div>

                        <div class="clear"></div>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                {% if page.items.count() > 0 %}
                {% for item in page.items %}
                <tr>
                    <td>
                        <input type="checkbox" name="fbulkid[]" value="{{ item.id }}" {% if formData['fbulkid'] is defined %}{% for key, value in formData['fbulkid'] if value == item.id %}checked="checked"{% endfor %}{% endif %} />
                    </td>
                    <td><span class="badge badge-primary">{{ item.id }}</span></td>
                    <td>
                        <div class="media">
                            <a class="pull-left" href="">
                                <img src="{{ item.getSmallImage() }}" width="50" height="50" class="media-object img-circle"/>   
                            </a>
                            <div class="media-body">
                                <h5 style="line-height: 10px;"><a href="{{ this.config.application.baseUriAdmin }}user/edit/{{ this.session.get('me').id }}">{{ item.name|escape }}</a></h5>
                                <ul class="list-unstyled">
                                    <li>Email: <strong>{{ item.email }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                    <td><span class="label label-{{ item.getRoleLabel() }}">{{ item.getRoleName() }}</span></td>
                    <td>
                        <span class="label label-{{ item.getStatusLabel() }}">{{ item.getStatusName()|upper }}</span>
                    </td>
                    <td>{{ item.created_at }}</td>
                    <td>
                        <div class="btn-group pull-right">
                        <a href="{{ config.application.baseUriAdmin }}user/edit/{{ item.id }}" class="btn btn-default btn-xs"><i class="fa fa-cog"></i></a>
                        <a href="javascript:delm('{{ config.application.baseUriAdmin }}user/delete/{{ item.id }}/{{ redirectUrl }}');" class="btn btn-default btn-xs" {% if item.status == 1 %}disabled{% endif %}><i class="fa fa-trash-o"></i></a>
                        </div>
                    </td>
                </tr>
                {% endfor %}
                {% else %}
                <tr>
                    <td colspan="9"> Data Notfound!</td>
                </tr>
                {% endif %}
            </tbody>
            </table>
        </form>
        </div>
    </div>

    <script type="text/javascript">

        function gosearch()
        {
            var path = rooturl_admin + "user";

            var keyword = $("#keyword").val();
            if(keyword.length > 0)
            {
                path += "?keyword=" + keyword;
            }

            document.location.href= path;
        }
    </script>


{% endblock %}
