<div class="page">
    <h1>
        Project Results for {{ project.key }}
    </h1>

    <div class="form-group" style="padding-bottom: 50px; ">
        <div class="pull-left">
            <ul class="nav navbar-nav" >

                <li ui-sref-active="active" dropdown>
                    <a dropdown-toggle class="btn btn-default" style="padding: 6px;"><span class="glyphicon glyphicon-floppy-save"></span> Export Project Model <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                    <li><a href="/server/projects/export_model/JSON/{{ project.key }}"  target="download_iframe">JSON</a></li>
                    <li><a href="/server/projects/export_model/CSV/{{ project.key }}"  target="download_iframe">CSV</a></li>
                    </ul>
                </li>
            </ul>  
        </div>
        <div class="pull-right">
            <a ui-sref="/projects/overview" class="btn btn-default"><span class="glyphicon glyphicon-list"></span> View All Projects</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading pointer collapsible" ng-click="participants_collapse = !participants_collapse">
            <h3 class="panel-title">
                <span class="glyphicon glyphicon-user upb-blue"></span> Participant Results
                <span class="pull-right">
                    <button class="btn btn-default btn-circle-xs">
                        <span class="glyphicon glyphicon-chevron-up" ng-show="!participants_collapse"></span>
                        <span class="glyphicon glyphicon-chevron-down" ng-show="participants_collapse"></span>
                    </button>
                </span>
            </h3>
        </div>
        <div class="panel-body" collapse="participants_collapse">
            <div style="padding-bottom: 25px;">
                <h4>
                    Seed Participant: <span class="upb-blue"><span class="glyphicon glyphicon-user"></span> {{ get_seed() }}</span>
                </h4>
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th style="width: 5%;">
                        Order
                    </th>
                    <th style="width: 15%;">
                        First Name
                    </th>
                    <th style="width: 15%;">
                        Last Name
                    </th>
                    <th style="width: 25%;">
                        Email Address
                    </th>
                    <th style="width: 15%;">
                        Status
                    </th>
                    <th style="width: 25%;">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="participant in participants">
                    <td class="text-center text-primary">
                        <strong>{{ participant.order }}</strong>
                    </td>
                    <td>
                        <strong>{{ participant.first_name }}</strong>
                    </td>
                    <td>
                        <strong>{{ participant.last_name }}</strong>
                    </td>
                    <td>
                        {{ participant.email }}
                    </td>
                    <td>
                        <span class="label text-uppercase" ng-class="get_participant_status_label_class(participant.status);">{{ participant.status }}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a ng-disabled="participant.status != 'COMPLETED'" href="/frontend/projects/participant_results/{{ project.key }}/{{ participant.id }}" class="btn btn-info"><span class="glyphicon glyphicon-eye-open"></span> View Model</a>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <ul class="nav navbar-nav">

                                <li ui-sref-active="active" dropdown >
                                    <a ng-disabled="participant.status != 'COMPLETED'" dropdown-toggle class="btn btn-success" style="background-color:#419641;padding:4px;font-size: 12px;"><span class="glyphicon glyphicon-floppy-save"></span> Export Model <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                    <li><a href="/server/projects/participant_results/export_model/JSON/{{ project.key }}/{{ participant.id }}"  target="download_iframe">JSON</a></li>
                                    <li><a href="/server/projects/participant_results/export_model/CSV/{{ project.key }}/{{ participant.id }}"  target="download_iframe">CSV</a></li>
                                    </ul>
                                </li>
                            </ul>  
                      </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="text-right">
                {{ participants.length }} {{ participants.length == 1 ? "Participant" : "Participants" }}
            </p>
        </div>
    </div>
</div>