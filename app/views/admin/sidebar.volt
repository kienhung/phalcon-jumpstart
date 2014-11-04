    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a class="dropdown-toggle account-overview-header" data-toggle="dropdown" href="#">
                    <div class="header-gravatar">
                        <img src="{{ session.get('me').getSmallImage() }}" class="ico ico-header-gravatar">
                    </div>
                    <div class="account-overview-user-details">
                      <p class="account-overview-shop-name">{{ session.get('me').name }}</p>
                      <p class="account-overview-user-name">
                          <span class="label label-{{ session.get('me').getRoleLabel() }}">{{ session.get('me').getRoleName() }}</span>
                          <span class="caret"></span>
                      </p>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ config.application.baseUriAdmin }}profile/changepassword"><i class="fa fa-key"></i>&nbsp; Change Password</a></li>
                    <li><a href="{{ config.application.baseUriAdmin }}profile"><i class="fa fa-user"></i>&nbsp;  Profile</a></li>
                    <li class="divider" style="background-color:#333;"></li>
                    <li><a href="{{ config.application.baseUriAdmin }}logout"><i class="fa fa-sign-out"></i>&nbsp; Sign Out</a></li>
                </ul>
            </li>

            <h2>ADMINISTRATOR</h2>
            <li id="menu_dashboard">
                <a href="{{ config.application.baseUriAdmin }}dashboard">Dashboard</a>
            </li>
            <li id="menu_generator">
                <a href="{{ config.application.baseUriAdmin }}generator">Code Generator</a>
            </li>
            <li id="menu_user">
                <a href="{{ config.application.baseUriAdmin }}user">User List</a>
            </li>
            <li id="menu_crontask">
                <a href="{{ config.application.baseUriAdmin }}crontask">Cron Task</a>
            </li>
            <li id="menu_logs">
                <a href="{{ config.application.baseUriAdmin }}logs">Logs</a>
            </li>
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->
