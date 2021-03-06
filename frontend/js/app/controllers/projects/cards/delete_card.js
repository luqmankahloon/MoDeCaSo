/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.0
 *
 * File:            /frontend/js/app/controllers/projects/cards/delete_card.js
 * Created:         2014-12-17
 * Author:          Peter Folta <mail@peterfolta.net>
 */

controllers.controller(
    "delete_card_controller",
    [
        "$scope",
        "$rootScope",
        "$http",
        "session_service",
        "project_key",
        "card_id",
        function($scope, $rootScope, $http, session_service, project_key, card_id)
        {
            $scope.project_key = project_key;
            $scope.card_id = card_id;

            $scope.flash = {
                "show":     false,
                "type":     null,
                "message":  null
            };

            $scope.delete_card = function()
            {
                /*
                 * Disable form elements to prevent duplicate requests
                 */
                $("#delete_card_submit_button").prop("disabled", true);
                $("#delete_card_cancel_button").prop("disabled", true);

                $http({
                    method:     "post",
                    url:        "/server/projects/" + $scope.project_key + "/cards/delete_card",
                    data:       {
                        card_id:            $scope.card_id
                    }
                }).then(
                    function(response)
                    {
                        /*
                         * Enable form elements
                         */
                        $("#delete_card_submit_button").prop("disabled", false);
                        $("#delete_card_cancel_button").prop("disabled", false);

                        $scope.flash.show = true;
                        $scope.flash.type = "alert-success";
                        $scope.flash.message = "<span class='glyphicon glyphicon-ok-sign'></span> <strong>" + get_success_title() + "</strong> The card has been successfully deleted.";

                        /*
                         * Disable submit button and change Cancel button to show "Close" instead
                         */
                        $("#delete_card_submit_button").prop("disabled", true);
                        $("#delete_card_cancel_button").html("Close");

                        $rootScope.$broadcast("load_project");
                    },
                    function(response)
                    {
                        /*
                         * Enable form elements
                         */
                        $("#delete_card_submit_button").prop("disabled", false);
                        $("#delete_card_cancel_button").prop("disabled", false);

                        $scope.flash.show = true;
                        $scope.flash.type = "alert-danger";
                        $scope.flash.message = "<span class='glyphicon glyphicon-exclamation-sign'></span> <strong>" + get_error_title() + "</strong> The card could not be deleted.";

                        shake_element($("#delete_card_flash"));
                    }
                );
            }
        }
    ]
);