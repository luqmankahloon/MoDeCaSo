<div class="page">
    <h1>
        Project Analysis Results for {{ project.key }}
    </h1>

    <div class="form-group" style="padding-bottom: 50px; ">
        <div class="pull-right">
            <a ui-sref="/projects/overview" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> View All Projects</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading pointer collapsible" ng-click="participants_collapse = !participants_collapse">
            <h3 class="panel-title">
                <span class="glyphicon glyphicon-user upb-blue"></span> Analyser Results
                <span class="pull-right">
                    <button class="btn btn-default btn-circle-xs">
                        <span class="glyphicon glyphicon-chevron-up" ng-show="!participants_collapse"></span>
                        <span class="glyphicon glyphicon-chevron-down" ng-show="participants_collapse"></span>
                    </button>
                </span>
            </h3>
        </div>
        <div class="panel-body" collapse="participants_collapse">

            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="width: 5%;">
                        Order
                    </th>
                    <th style="width: 10%;">
                        First Name
                    </th>
                    <th style="width: 10%;">
                        Last Name
                    </th>
                    <th style="width: 20%;">
                        Email Address
                    </th>
                    <th style="width: 30%;">
                        Comments
                    </th>
                    <th style="width: 25%;">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="analyser in analysers">
                    <td class="text-center text-primary">
                        <strong>{{ $index+1 }}</strong>
                    </td>
                    <td>
                        <strong>{{ analyser.user_data.first_name }}</strong>
                    </td>
                    <td>
                        <strong>{{ analyser.user_data.last_name }}</strong>
                    </td>
                    <td>
                        {{ analyser.user_data.email }}
                    </td>
                    <td ng-bind-html="html_save(analyser.comment)">
                        
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="/frontend/projects/analysis/analyser_results/{{ project.key }}/{{ analyser.user_data.username }}/{{ analyser.id }}" class="btn btn-info"><span class="glyphicon glyphicon-eye-open"></span> View Model</a>
                            
                        </div>
                        <div class="btn-group btn-group-sm">
                            <ul class="nav navbar-nav" >

                                <li ui-sref-active="active" dropdown>
                                    <a dropdown-toggle class="btn btn-success" style="background-color:#419641;padding:4px;font-size: 12px;"><span class="glyphicon glyphicon-floppy-save"></span> Export Model <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                    <li><a href="/server/analysis_results/export_model/JSON/{{ project.key }}/{{ analyser.user_data.username }}/{{ analyser.id }}"  target="download_iframe">JSON</a></li>
                                    <li><a href="/server/analysis_results/export_model/CSV/{{ project.key }}/{{ analyser.user_data.username }}/{{ analyser.id }}"  target="download_iframe">CSV</a></li>
                                    </ul>
                                </li>
                            </ul>  
                      </div>
                        
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="text-right">
                {{ analysers.length }} {{ analysers.length == 1 ? "Result" : "Results" }}
            </p>
        </div>
    </div>
</div>