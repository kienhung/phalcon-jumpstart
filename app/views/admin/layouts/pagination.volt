
    <ul class="pagination">
        {% set mid_range = 7 %}

        {% if page.total_pages > 10 %}
            {% if page.current != 1 and page.total_items >= 10 %}
                {% set pageString = '<li>'~ linkTo(""~paginateUrl~"&page="~page.before, "&laquo") ~'</li>' %}
            {% else %}
                {% set pageString = '<li style="display:none">'~ linkTo("#", "&laquo") ~'</li>' %}
            {% endif %}

            {% set start_range = page.current - (mid_range / 2)|floor %}
            {% set end_range = page.current + (mid_range / 2)|floor %}

            {% if start_range <= 0 %}
                {% set end_range = end_range + (start_range)|abs + 1 %}
                {% set start_range = 1 %}
            {% endif %}

            {% if end_range > page.total_pages %}
                {% set start_range = start_range - (end_range - page.total_pages) %}
                {% set end_range = page.total_pages %}
            {% endif %}

            {% set range = range(start_range, end_range) %}

            {% for i in 1..page.total_pages %}
                {% if range[0] > 2 and i == range[0] %}
                    {% set pageString = pageString ~ '<li><a> ... </a></li>' %}
                {% endif %}

                {% if i == 1 or i == page.total_pages or i in range %}    
                    {% if i == page.current %}
                        {% set pageString = pageString ~ '<li class="active">'~ linkTo(""~paginateUrl~"&page="~i, ""~i) ~'</li>' %}
                    {% else %}
                        {% set pageString = pageString ~ '<li>'~ linkTo(""~paginateUrl~"&page="~i, ""~i) ~'</li>' %}
                    {% endif %}
                {% endif %}

                
                {% if range[mid_range - 1] < (page.total_pages - 1) and i == range[mid_range - 1] %}
                    {% set pageString = pageString ~ '<li><a> ... </a></li>' %}
                {% endif %}
                
            {% endfor %}
            
            {% if page.current != page.total_pages and page.total_items >= 10 %}
                {% set pageString = pageString ~ '<li>'~ linkTo(""~paginateUrl~"&page="~page.next, "&raquo") ~'</li>' %}
            {% else %}
                {% set pageString = pageString ~ '<li style="display:none">'~ linkTo("#", "&raquo") ~'</li>' %}
            {% endif %}
            
            {{ pageString }}
            
        {% else %}
        
            {% if page.current != 1 %}
                <li>{{ linkTo(""~paginateUrl~"&page=1", "First") }}</li>
                <li>{{ linkTo(""~paginateUrl~"&page="~page.before, "&laquo") }}</li>
            {% endif %}

            {% for i in 1..page.total_pages %}
                <li {% if i == page.current %}class="active"{% endif %}>{{ linkTo(""~paginateUrl~"&page="~i, ""~i) }}</li>
            {% endfor %}

            {% if page.current != page.total_pages %}
                <li>{{ linkTo(""~paginateUrl~"&page="~page.next, "&raquo") }}</li>
                <li>{{ linkTo(""~paginateUrl~"&page="~page.last, "Last") }}</li>
            {% endif %}
        {% endif %}

        
    </ul>
