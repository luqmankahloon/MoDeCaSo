<div class="modal-header">
    <button type="button" class="close" ng-click="$close()" tooltip="Close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h3 class="modal-title text-success">
        Thank you!
    </h3>
</div>
<div class="modal-body" style="padding-bottom: 0;">
    <p>
        Your submission was successful.
    </p>
    <p>
        Click on "Close" to see your Model.
    </p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" ui-sref="/frontend/projects/analysis/analyser_results/{{ key }}/{{ user_name }}" ng-click="$close();">Close</button>
</div>