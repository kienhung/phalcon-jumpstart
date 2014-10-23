{% extends "layouts/main.volt" %}

{% block content %}
    <div class="header-row">
        <div class="header-row-wrapper">
            <header>
                <h1 class="header-main" id="page-header" rel="menu_{{MODULE_LOWER}}">
                    <a href="javascript:void(0)" id="menu-toggle" style="color:#333;"><i class="fa {{FA_ICON}}"></i></a>
                    <a href="{{ config.application.baseUriAdmin }}{{MODULE_LOWER}}" style="color:#333;">{{ lang._('IndexTitle') }}</a> (<span class="delete-decrease-count">{% if page.total_items > 0 %}{{ page.total_items }}{% else %}0{% endif %}</span>)
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
                <a class="btn btn-sm btn-success pull-right" href="{{ config.application.baseUriAdmin }}{{MODULE_LOWER}}/add" style="margin-right: 15px;"><i class="fa fa-plus"></i> &nbsp; {{ lang._('IndexAddButton') }}</a>
                <!-- Search field -->
                <div class="col-md-4 col-xs-6 pull-right">
                    <div class="input-group">
                        <input type="text" id="keyword" class="form-control input-sm" placeholder="Search..." value="{% if formData['keyword'] is defined %}{{ formData['keyword'] }}{% endif %}" style="font-size:13px;"/>
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
        	<input type="hidden" name="searchurl" id="searchurl" value="{{ config.application.baseUriAdmin }}{{MODULE_LOWER}}" />
            <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />
            <table class="table table-hover" style="margin-top:10px">
            <thead>
                <tr>
                    {{TABLE_HEAD}}
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="{{COLSPAN_FOOTER}}">

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
                {{TABLE_BODY}}
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
