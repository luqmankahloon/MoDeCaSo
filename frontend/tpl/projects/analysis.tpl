<div class="page">
    <h1>
        Project Analysis for {{ key }}
    </h1>
    <!--
    <div class="form-group" style="padding-bottom: 50px; ">
        <div class="pull-right">
            <a ui-sref="/projects/overview" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> View All Projects</a>
        </div>
    </div>
    -->
    <div id="analysis-chart">
        <div class="panel-body" >
            <span class="upb-blue"><span class="glyphicon glyphicon-user"></span> <strong>Category Analysis</strong></span>
        </div>
        <div class="form-group" style="padding-bottom: 25px; ">
            <div class="pull-left">
                <button type="button" class="btn btn-success" ng-click="show_solution()"  ng-disabled="selectedCategoriesLength == 0;"><span class="glyphicon glyphicon-play"></span> Show Suggested Solution</button>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-info" ng-click="show_info_message();"><span class="glyphicon glyphicon-info-sign"></span> Info Message</button>
            </div>
        </div>
   
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th style="width: 80%;">
                        Graph
                    </th>
                    <th style="width: 20%;">
                        Selected Categories
                    </th>
                </tr>
            </thead>
            <tbody>              
                <tr>
                    <td id="graph-width">
                        <nvd3 options="options" data="data" class="my-chart"></nvd3>
                    </td>
                    <td>
                     <table class="table table-bordered table-striped">
                        <tr>
                            <td> 
                                <span class="label text-uppercase label-success">Selected</span>
                                <span class="label text-uppercase label-default">Not Selected</span>
                            </td>
                        </tr>
                        <tr ng-repeat="key in unSortedKeys">
                            <td> <span class="label text-uppercase" ng-class="get_category_status_label_class(selectedCategories[key]);">{{key}}</span></td>
                        </tr>
                        </table>
                        
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="text-right">
        </p>
    </div>
        
   
    <div id="solution-chart" style="display: none">
        <div class="panel-body" >
            <span class="upb-blue"><span class="glyphicon glyphicon-user"></span> <strong>Suggested Solution</strong></span>
        </div>
   
        <div class="form-group" style="padding-bottom: 25px; ">
            <div class="pull-left">
                <button type="button" class="btn btn-success" ng-click="save_final_model();" ng-disabled="unsorted_cards.length != 0;"><span class="glyphicon glyphicon-log-out"></span> Select as final model</button>
                <button type="button" class="btn btn-default" ng-click="back_analysis();"><span class="glyphicon glyphicon-refresh"></span> Back to Category Analysis</button>
            </div>

            <div class="pull-right">
                <button type="button" class="btn btn-info" ng-click="add_comment();"><span class="glyphicon glyphicon-info-sign"></span> Add Comments</button>
                <button type="button" class="btn btn-info" ng-click="show_info_message();"><span class="glyphicon glyphicon-info-sign"></span> Info Message</button>
            </div>
        </div>

        <div class="alert alert-dismissable" ng-show="status_flash.show" ng-class="status_flash.type" ng-bind-html="html_save(status_flash.message)" role="alert"></div>
        
        
        <div class="experiment-container" style="margin-bottom: 100px;">
            <div class="card-container-col sortable" ui-sortable="sortable_options" ng-model="unsorted_cards">
                <div class="card grab" ng-repeat="card in unsorted_cards">
                    <div class="btn-group btn-group-sm card-controls" style="visibility: visible;">
                        <button ng-class="get_tooltip_class(card.tooltip);" class="btn btn-info" tooltip="{{ card.tooltip }}" tooltip-append-to-body="true"><span class="glyphicon glyphicon-question-sign"></span></button>
                        <button ng-class="get_tooltip_class(card.info);" class="btn btn-info" tooltip-html-unsafe="{{ card.info }}" tooltip-append-to-body="true"><span class="glyphicon glyphicon-question-sign"></span></button>
                    </div>
                    <div class="card-text">
                        {{ card.text }}
                    </div>
                </div>
            </div>
            <div class="workspace">
                <div class="workspace-blank" ng-show="model_categories.length == 0;">
                    Start here
                </div>
                <div class="category-wrapper" ng-show="model_categories.length > 0;">
                    <div class="category" ng-repeat="category in model_categories">
                        <div class="category-header">
                            {{ category.text }}
                        </div>
                        <div ui-sortable="sortable_options" class="sortable sortable-target" ng-model="category.cards">
                            <div class="card grab" ng-repeat="card in category.cards">
                                <div class="btn-group btn-group-sm card-controls" style="visibility: visible;">
                                    <button ng-class="get_tooltip_class(card.tooltip);" class="btn btn-info" tooltip="{{ card.tooltip }}" tooltip-append-to-body="true"><span class="glyphicon glyphicon-question-sign"></span></button>
                                    <button ng-class="get_tooltip_class(card.info);" class="btn btn-info" tooltip-html-unsafe="{{ card.info }}" tooltip-append-to-body="true"><span class="glyphicon glyphicon-question-sign"></span></button>
                                </div>
                                <div class="card-text">
                                    {{ card.text }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>