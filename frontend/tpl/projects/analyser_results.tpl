<div class="page">
    <h1>Analysis Result of {{ user.first_name }} {{ user.last_name }} ({{ project_key }})</h1>

    <div class="form-group" style="padding-bottom: 50px; ">
        <div class="pull-left">
            <a class="btn btn-default" href="/frontend/projects/analysis/results/{{ project_key }}"><span class="glyphicon glyphicon-arrow-left"></span> Back to Analysis Results</a>
        </div>
        <div class="pull-right">
            <ul class="nav navbar-nav" >

                <li ui-sref-active="active" dropdown>
                    <a dropdown-toggle class="btn btn-default" style="padding: 6px;"><span class="glyphicon glyphicon-floppy-save"></span> Export Model <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                    <li><a href="/server/analysis_results/export_model/JSON/{{ project_key }}/{{ user.username }}/{{ analysis_id }}"  target="download_iframe">JSON</a></li>
                    <li><a href="/server/analysis_results/export_model/CSV/{{ project_key }}/{{ user.username }}/{{ analysis_id }}"  target="download_iframe">CSV</a></li>
                    </ul>
                </li>
            </ul>  
        </div>
    </div>

    <div class="experiment-container">
        <div class="workspace" style="left: 0px !important;">
            <div class="category-wrapper">
                <div class="category" ng-repeat="category in categories">
                    <div class="category-header">
                        {{ category.text }}
                    </div>
                    <div class="sortable sortable-target">
                        <div class="card" ng-repeat="card in category.cards">
                            <div class="btn-group btn-group-sm card-controls" style="visibility: visible;">
                                <button ng-class="get_tooltip_class(card.tooltip);" class="btn btn-info" tooltip="{{ card.tooltip }}" tooltip-append-to-body="true"><span class="glyphicon glyphicon-question-sign"></span></button>
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