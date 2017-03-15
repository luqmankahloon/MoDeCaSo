<!-- Created by Luqman Ahmad -->
<div class="modal-header">
    <h3 class="modal-title">
        Information!
    </h3>
</div>
<div class="modal-body" style="padding-bottom: 0;">
    <p id="welcome_message" ng-bind-html="html_save(message)"></p>
</div>
<div class="modal-footer">
    <button id="welcome_start_button" type="button" class="btn btn-success" ng-click="$close();"><span class="glyphicon glyphicon-play"></span> Start</button>
</div>