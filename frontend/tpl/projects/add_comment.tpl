
    <div class="modal-header">
        <button id="add_comment_close_button" type="button" class="close" ng-click="$close()" tooltip="Close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title">
            Add Comments to Model
        </h3>
    </div>
    <div class="modal-body" style="padding-bottom: 0;">

<p id="welcome_message" > Add comments to this model. This will be saved with the submission of your modal.</p>

        <div class="form-group" id="edit_message_message_group" style="padding: 20px 0;">
            <label for="edit_message_message" class="col-sm-2 control-label">Comments</label>
            <div class="input-group col-sm-10" style="padding-right: 15px;">
                <span class="input-group-addon" style="vertical-align: top;"><span class="glyphicon glyphicon-comment"></span></span>
                <textarea id="comment_text" class="form-control" rows="10" ng-model="comment" required style="resize: vertical;"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button id="add_comment_submit_button" type="button" class="btn btn-primary" ng-click="submit_comment();$close()"tabindex="7"><span class="glyphicon glyphicon-plus-sign"></span> Submit</button>
        <button id="add_comment_cancel_button" type="button" class="btn btn-default" ng-click="$close()" tabindex="8">Cancel</button>
    </div>
