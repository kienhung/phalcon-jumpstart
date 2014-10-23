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

    <form action="" id="myform" method="post" name="myform" class="form-horizontal" style="padding:0 10px;">
        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}" />

        <legend>MODEL settings</legend>
        <div class="alert alert-info" role="alert" style="line-height: 22px;">
            <strong>Constant Pattern Example: </strong> <code>STATUS_ENABLE:1:Enable,STATUS_DISABLE:3:Disable</code> <br/>
            <strong>Date time generate: </strong> Must be <code>created_at</code> or <code>updated_at</code> field name and type is <code>DATETIME</code>
            {# <br/>
            <strong>Rule: </strong> 
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <code>status</code> field must be locate at front of <code>created_at</code> field and begin with prefix <code>STATUS_</code></p> #}
        </div>

    <div class="form-group">
        <label for="fmodulenamespace" class="col-md-2 control-label">Namespace</label>
        <span class="col-md-3">
            <input type="text" name="fmodelnamespace" id="fmodelnamespace" placeholder="Model" value="Model" class="form-control">
        </span>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label" for="fmodel">Class</label>
        <div class="col-md-8">
            <span class="col-md-4 inner-left"><input type="text" name="fmodel" id="fmodel" value="{{ formData['modelName']|capitalize }}" class="form-control"></span>
            <span class="col-md-1 inner-left"><code>Extends</code></span>
            <span class="col-md-4"><input type="text" name="fmodelbaseclass" id="fmodelbaseclass" value="BaseModel" class="form-control" /></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label" for="fdboject">MySQL Database Object</label>
        <span class="col-md-1">
            <input type="text" name="fdbobject" id="fdbobject" value="db" placeholder="db" title="Default: db" class="form-control" />
        </span>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label">Mapping</label>
        <div class="col-md-10 pull-left">
            <table class="table table-hover" cellpadding="2" width="100%">
                    <thead>
                        <tr>
                            <th width="300">Column Name</th>
                            <th width="100"></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        {% for column in formData['columns'] %}
                        <tr>
                            <td><b>{{ column['name'] }}</b> <span class="label label-default">{{ column['typeName'] }}{% if column['size'] > 0 %}({{ column['size'] }}){% endif %}</span> 
                            {% if column['isPrimary'] == true %}
                                <span class="label label-danger">Primary</span></td>
                            {% else %}
                                {#
                                {{ dump(column['name']) }}
                                {{ dump(formData['localReferCol']) }}
                                #}
                                {% for foreignIndex in formData['localReferCol'] if formData['localReferCol'] != null and column['name'] == foreignIndex  %}
                                    <span class="label label-primary">ForeignKey</span>
                                {% endfor %}

                                {% for colIndex in formData['indexesCol'] if formData['indexesCol'] != null and column['name'] == colIndex %}
                                    <span class="label label-info">Index</span>
                                {% endfor %}
                                </td>
                            {% endif %}
                            <td><label class="checkbox"><input type="checkbox"  name="fsortable[{{ column['name'] }}]" value="1" {% if column['type'] == 2 or column['isPrimary'] == true or column['type'] == 6 %}disabled{% endif %}/> Sortable</label></td>
                            <td>
                            {% if column['isNumeric'] == true %}
                                {% if  column['isPrimary'] != true %}

                                    {% for foreignKey in formData['localReferCol'] if formData['localReferCol'] is defined and column['name'] == foreignKey %}
                                        <code>Relationship type </code>
                                        <select name="frelationtype[{{ column['name'] }}]">
                                            <option value="">----</option>            
                                            <option value="1-1">1 - 1</option>
                                            <option value="1-n">1 - n</option>
                                            <option value="n-1">n - 1</option>
                                            <option value="n-n">n - n</option>
                                        </select>
                                    {% endfor %}
                                {% else %}
                                    <input type="text" name="fconstantable[{{ column['name'] }}]" value="" placeholder="Constant Value" title="CONSTANT1:value1:text,CONSTANT2:value2:text2,..."  />     
                                
                                {% endif %}
                            {% else %}
                                <label class="checkbox"> <input type="checkbox" name="fsearchabletext[{{ column['name'] }}]" value="1" {% if column['type'] == 4 %}disabled{% endif %}> Searchable Text</label>
                            {% endif %}
                            </td>
                        </tr>
                        {% endfor %}
                    </tbody>

            </table>
        </div>
    </div>



    <fieldset class="admincontrollergenerator">
        <legend>CONTROLLER settings</legend>

        <div class="form-group">
            <label class="col-md-2 control-label" for="fcontrollericonclass">Font Awesome Icon Class</label>
            <span class="col-md-4 inner-left">
                <input type="text" name="fcontrollericonclass" id="fcontrollericonclass" value="" value="" placeholder="fa-info-circle" class="inline form-control" />
                <a href="http://fontawesome.io/icons/" target="_blank" title="Click here to explore all available Font Awesome Icon Classes"> Find icon class..</a>
            </span>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="fcontrollergroup">Namespace</label>
            <div class="col-md-4 input-group">
                <span class="input-group-addon"><code>Controller\</code></span>
                <input type="text" name="fcontrollernamespace" id="fcontrollernamespace" class="form-control" value="Admin" />
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label" for="fcontrollerclass">Class Name</label>
            <div class="col-md-8" style="padding-left:0;">
                <span class="col-md-4 inner-left">
                <input type="text" name="fcontrollerclass" id="fcontrollerclass" value="{{ formData['controllerClass'] }}Controller" class="inline form-control"/></span>
                <span class="col-md-1 inner-left">Extends</span> <span class="col-md-4 inner-left"><code>BaseController</code></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="fcontrollerrecordperpage">Record Per Page</label>
            <span class="col-md-4 inner-left">
                <input type="text" name="fcontrollerrecordperpage" id="fcontrollerrecordperpage" value="30" value="" placeholder="Default: 30" class="inline form-control" />
            </span>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label" for="fclassextend">Mapping</label>
            <div class="col-md-10 pull-left">


                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="200">Column Name</th>
                            <th width="120">Label</th>
                            <th width="200"></th>
                            <th>Validating in Add/Edit</th>
                        </tr>
                    </thead>

                    <tbody>
                        {% for column in formData['columns'] %}
                        <tr>
                            <td>
                                <b>{{column['name']}}</b>
                                <span class="label label-default">{{column['typeName']}} {% if column['size'] > 0 %}({{column['size']}})</span> {% endif %}
                                {% if column['isPrimary'] == 1 %} <span class="label label-danger">Primary</span> {% endif %}
                                {% if column['name'] in formData['indexesCol'] %}<span class="label label-info">Index</span>{% endif %}
                            </td>
                            <td>
                                <input type="text" class="input-small" name="flabel[{{column['name']}}]" value="{{column['label']}}" />
                            </td>
                            <td>
                                <label class="checkbox"><input type="checkbox" name="fexcludeindex[{{column['name']}}]"  value="1" />Index Exclude</label>
                            </td>
                            <td>
                                <label class="checkbox"><input type="checkbox" {% if column['isPrimary'] == 1 or column['name'] == 'created_at' or column['name'] == 'updated_at' %}disabled="disabled" checked="checked"{% endif %} name="fexclude[{{column['name']}}]"  value="1" />Add/Edit Exclude</label>
                            </td>
                            <td>
                                {% if column['isPrimary'] == false %}
                                <input type="button" name="advance-setting" onclick="showAdvanceSetting($(this))" data-id="col-{{ column['name'] }}" value="Advanced Setting" class="btn btn-sm btn-default" />
                                {% endif %}
                            </td>
                        </tr>
                        <tr id="col-{{ column['name'] }}" class="advance-setting-outer">
                            <td colspan="5">
                                <div class="advance-option">
                                    <table width="100%">
                                        <tr>
                                            <td width="110"><strong>Validate</strong></td>
                                            <td>
                                                <div class="col-md-3">
                                                    <select name="fvalidating[{{column['name']}}]" class="input-sm">
                                                        {% for key, validate in formData['validateType'] %}
                                                        <option value="{{ key }}">{{ validate }}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="110"><strong>Input type</strong></td>
                                            <td>
                                                <div class="row" style="margin:0 0 5px 0">
                                                    <div class="col-md-3">
                                                        <select name="finputtype[{{column['name']}}]" class="input-sm form-control" onchange="inputTypeChange($(this))">
                                                            {% for key, type in formData['inputType'] %}
                                                            <option value="{{ key }}" {% if key ==  column['inputType'] %}selected="selected"{% endif %}>{{ type }}</option>
                                                            {% endfor %}
                                                        </select>
                                                    </div>

                                                    <div class="model-choice expand-setting col-md-3">
                                                        <select name="fselectmodel[{{ column['name'] }}]" class="input-sm form-control" onchange="loadValueText($(this))">
                                                            {% for key, modelName in formData['includeModel'] %}
                                                            <option value="{{ modelName }}" >{{ modelName }}</option>
                                                            {% endfor %}
                                                        </select>
                                                    </div>
                                                    <div class="model-condition expand-setting col-md-6">
                                                        <input type="text" name="fselectcondition[{{column['name']}}]" class="input-sm form-control" />
                                                    </div>

                                                    <div class="expand-setting imageupload-setting">
                                                        <div class="select-value col-md-4" style="padding:0">
                                                            <select name="fimagename[{{column['name']}}]" class="input-sm form-control">
                                                            <option value="0">-- Select image name column --</option>
                                                            {% for col in formData['columns'] %}
                                                            <option value="{{ col['name'] }}">{{ col['name'] }}</option>
                                                            {% endfor %}
                                                            </select>
                                                        </div>
                                                        <!--<div class="select-value col-md-4">
                                                            <label class="checkbox">
                                                                <input type="checkbox" name="fmultiimage[{{column['name']}}]" value="1" /> Multiple
                                                            </label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                                <div class="row expand-setting select-setting" style="margin:0">
                                                    <div class="col-md-3">&nbsp;</div>

                                                    <div class="select-value col-md-3" style="padding:0">
                                                        <select name="fselectvalue[{{column['name']}}]" class="input-sm form-control">
                                                            <option value="0">-- Select value column --</option>
                                                            {% for key, column in formData['columnModel'] %}
                                                            <option value="{{ column['name'] }}">{{ column['name'] }}</option>
                                                            {% endfor %}
                                                        </select>
                                                    </div>

                                                    <div class="select-text col-md-3">
                                                        <select name="fselecttext[{{column['name']}}]" class="input-sm form-control">
                                                            <option value="0">-- Select text column --</option>
                                                            {% for key, column in formData['columnModel'] %}
                                                            <option value="{{ column['name'] }}">{{ column['name'] }}</option>
                                                            {% endfor %}
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        {% endfor %}

                    </tbody>
                </table>

            </div>
        </div>
    </fieldset>



    <div class="row section buttons">
        <div class="pull-left">
            <code>ALWAYS OVERWRITE EXISTED FILES</code>
            <!-- <input type="checkbox" name="foverwrite" id="foverwrite"  value="1" />
            <label class="inline" for="foverwrite">Overwrite Existed files</label> -->
        </div>
        <input type="submit" name="fsubmit" value="GENERATE NOW" class="btn btn-success" />
    </div>

</form>

{% endblock %}