{% extends "layouts/main.volt" %}

{% block content %}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_crontask">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa fa-clock-o"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}crontask" style="color:#333;">{{ lang._('IndexTitle') }}</a> (<span class="delete-decrease-count">{% if page.total_items > 0 %}{{ page.total_items }}{% else %}0{% endif %}</span>)
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
        <li class="active"><a href="#index-listing" role="tab" data-toggle="tab">{{ lang._('TabListing') }}</a></li>
        <li style="width:80%; margin-left:2px; margin-top:4px;" class="pull-right">
            <div class="row">
                <!-- Search field -->
                <div class="col-md-4 col-xs-6 pull-right">
                    <div class="input-group">
                        <input type="text" id="keyword" class="form-control input-sm" placeholder="Search in Task, Action ... " value="{% if formData['keyword'] is defined %}{{ formData['keyword'] }}{% endif %}" style="font-size:13px;"/>
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
        <form method="post" action="" onsubmit="return confirm('Are You Sure ?');">
        	<input type="hidden" name="searchurl" id="searchurl" value="{{ config.application.baseUriAdmin }}crontask" />
            <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
            <table class="table table-hover" style="margin-top:10px">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" class="check-all"/></th>
					<th id="id">{{ lang._('LabelId') }}</th>
					<th id="task">{{ lang._('LabelTask') }}</th>
					<th id="action">{{ lang._('LabelAction') }}</th>
					<th id="ipaddress">{{ lang._('LabelIpaddress') }}</th>
					<th id="timeprocessing"><a href="{{ config.application.baseUriAdmin }}crontask?sortby=timeprocessing&sorttype={% if formData['sorttype']|upper == 'DESC'%}ASC{% else %}DESC{% endif %}{% if formData['keyword'] is defined %}&keyword={{ formData['keyword'] }}{% endif %}">{{ lang._('LabelTimeprocessing') }}</a></th>
					<th id="output">{{ lang._('LabelOutput') }}</th>
					<th id="status"><a href="{{ config.application.baseUriAdmin }}crontask?sortby=status&sorttype={% if formData['sorttype']|upper == 'DESC'%}ASC{% else %}DESC{% endif %}{% if formData['keyword'] is defined %}&keyword={{ formData['keyword'] }}{% endif %}">{{ lang._('LabelStatus') }}</a></th>
					<th id="created_at"><a href="{{ config.application.baseUriAdmin }}crontask?sortby=createdat&sorttype={% if formData['sorttype']|upper == 'DESC'%}ASC{% else %}DESC{% endif %}{% if formData['keyword'] is defined %}&keyword={{ formData['keyword'] }}{% endif %}">{{ lang._('LabelCreatedat') }}</a></th>
					<th width="100"></th>

                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="10">

                        <div class="bulk-actions align-left">
                            <select name="fbulkaction">
                                <option value="">---- Select Action ----</option>
                                <option value="delete">Delete</option>
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
                        <td><input type="checkbox" name="fbulkid[]" value="{{ item.id }}" {% if formData['fbulkid'] is defined %}{% for key, value in formData['fbulkid'] if value == item.id %}checked="checked"{% endfor %}{% endif %} /></td>
                        <td>{{ item.id }}</td>
                        <td><span class="label label-primary">{{ item.task|escape }}</span></td>
                        <td>{{ item.action|escape }}</td>
                        <td><code>{{item.ipaddress|escape }}</code></td>
                        <td>{{ item.timeprocessing }}</td>
                        <td>{{ item.output|escape }}</td>
                        <td>
                            <span class="label label-{{ item.getStatusLabel() }}">
                                {{ item.getStatusName()|upper }}
                            </span>
                        </td>
                        <td>{{item.created_at}}</td>
                        <td>
                            <div class="btn-group pull-right">
                                <a href="javascript:delm('{{ config.application.baseUriAdmin }}crontask/delete/{{ item.id }}/{{ redirectUrl }}');" class="btn btn-default btn-xs"><i class="fa fa-trash-o"></i></a>
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
        	var path = $('#searchurl').val();

            var keyword = $("#keyword").val();
            if(keyword.length > 0)
            {
                path += "?keyword=" + keyword;
            }

            document.location.href= path;
        }
    </script>


{% endblock %}
