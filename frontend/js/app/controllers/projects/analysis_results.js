/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /frontend/js/app/controllers/projects/analysis_results.js
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

controllers.controller(
    "analysis_results_controller",
    [
        "$scope",
        "$rootScope",
        "$http",
        "session_service",
        "key",
        function($scope, $rootScope, $http, session_service, key)
        {
            $scope.key = key;

            $scope.participants_collapse = false;

            $scope.load_results = function()
            {
                $http({
                    method:     "get",
                    url:        "/server/analysis_results/get_analysis_list/" + $scope.key
                }).then(
                    function(response)
                    {
                        $scope.project   = response.data.project;
                        $scope.analysers = response.data.analysers;
                        for(i = 0 ; i < $scope.analysers.length; i++) {    
                                $scope.analysers[i].comment=$scope.analysers[i].comment.replace(/\n/gi, "<br>");      
                        }
                       
                    }
                );
            };

            $scope.$on(
                "load_results",
                function(event, args)
                {
                    $scope.load_results();
                }
            );

            $rootScope.$broadcast("load_results");
        }
    ]
);