/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.0
 *
 * File:            /frontend/js/app/controllers/projects/cards/delete_all_cards.js
 * Created:         2014-12-22
 * Author:          Peter Folta <mail@peterfolta.net>
 */

controllers.controller(
    "delete_all_cards_controller",
    [
        "$scope",
        "$rootScope",
        "$http",
        "session_service",
        "project_key",
        function($scope, $rootScope, $http, session_service, project_key)
        {
            $scope.project_key = project_key;

            $scope.flash = {
                "show":     false,
                "type":     null,
                "message":  null
            };

            $scope.delete_all_cards = function()
            {
                /*
                 * Disable form elements to prevent duplicate requests
                 */
                $("#delete_all_cards_submit_button").prop("disabled", true);
                $("#delete_all_cards_cancel_button").prop("disabled", true);

                $http({
                    method:     "get",
                    url:        "/server/projects/" + $scope.project_key + "/cards/delete_all_cards"
                }).then(
                    function(response)
                    {
                        /*
                         * Enable form elements
                         */
                        $("#delete_all_cards_submit_button").prop("disabled", false);
                        $("#delete_all_cards_cancel_button").prop("disabled", false);

                        $scope.flash.show = true;
                        $scope.flash.type = "alert-success";
                        $scope.flash.message = "<span class='glyphicon glyphicon-ok-sign'></span> <strong>" + get_success_title() + "</strong> All cards have been successfully deleted.";

                        /*
                         * Disable submit button and change Cancel button to show "Close" instead
                         */
                        $("#delete_all_cards_submit_button").prop("disabled", true);
                        $("#delete_all_cards_cancel_button").html("Close");

                        $rootScope.$broadcast("load_project");
                    },
                    function(response)
                    {
                        /*
                         * Enable form elements
                         */
                        $("#delete_all_cards_submit_button").prop("disabled", false);
                        $("#delete_all_cards_cancel_button").prop("disabled", false);

                        $scope.flash.show = true;
                        $scope.flash.type = "alert-danger";
                        $scope.flash.message = "<span class='glyphicon glyphicon-exclamation-sign'></span> <strong>" + get_error_title() + "</strong> The cards could not be deleted.";

                        shake_element($("#delete_all_cards_flash"));
                    }
                );
            }
        }
    ]
);