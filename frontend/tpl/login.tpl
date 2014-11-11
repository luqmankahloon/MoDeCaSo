<div id="login_background">
</div>
<div id="login_container">
    <div class="login_form" id="login_form">
        <div class="alert" id="login_flash" ng-show="flash.show" ng-class="flash.type" ng-bind-html="html_save(flash.message)" role="alert">
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-log-in"></span> <strong>Log In</strong>
                </h3>
            </div>
            <div class="panel-body">
                <img src="/frontend/img/upb-logo.svg">
                <h3 class="text-center">
                    MoDeCaSo
                </h3>
                <form role="form" name="login_form" ng-submit="login()" novalidate>
                    <div class="form-group" id="login_username_group">
                        <label for="login_username" class="control-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="text" ng-model="login.username" id="login_username" placeholder="Username" required autofocus tabindex="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" id="login_password_group">
                        <label for="login_password" class="control-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            <input type="password" ng-model="login.password" id="login_password" placeholder="Password" required tabindex="2" class="form-control">
                        </div>
                    </div>
                    <button type="submit" ng-disabled="login_form.$invalid" class="btn btn-primary pull-right" tabindex="3"><span class="glyphicon glyphicon-log-in"></span> Log In</button>
                </form>
            </div>
        </div>
    </div>
</div>