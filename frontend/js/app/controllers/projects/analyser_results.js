/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /frontend/js/app/controllers/projects/analyser_results.js
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

controllers.controller(
    "analyser_results_controller",
    [
        "$scope",
        "$http",
        "$modal",
        "project_key",
        "user_name",
        "model_id",
        function($scope, $http, $modal, project_key, user_name, model_id)
        {
            $scope.project_key = project_key;
            $scope.user_name = user_name;
            $scope.model_id = model_id;

            $scope.categories = [];

            $scope.$watch(
                function()
                {
                    return $scope.categories.length;
                },

                function(value)
                {
                    $(".category-wrapper").css("min-width", (value * 239) + "px");
                }
            );

            $scope.get_tooltip_class = function (tooltip)
            {
                if (!tooltip) {
                    return "dontshow";
                }
            };

            $http({
                method:     "get",
                url:        "/server/analysis_results/get_analyser_results/" + $scope.project_key + "/" + $scope.user_name + "/" + $scope.model_id
            }).then(
                function(response)
                {
                    //console.log($scope);
                    $scope.user       = response.data.user;
                    $scope.analysis_id = response.data.analysis_id;
                    $scope.categories = response.data.categories;

                    for (var i = 0; i < $scope.categories.length; i++) {
                        $scope.categories[i].cards.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                    }
                }
            );
        }
    ]
);