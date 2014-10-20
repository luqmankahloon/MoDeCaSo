/*
 * UPB-BTHESIS
 * Copyright (C) 2004-2014 Peter Folta. All rights reserved.
 *
 * Project:			UPB-BTHESIS
 * Version:			0.0.1
 *
 * File:            /frontend/js/app/controllers/login.js
 * Created:			2014-10-19
 * Last modified:	2014-10-20
 * Author:			Peter Folta <mail@peterfolta.net>
 */

controllers.controller(
    "loginCtrl",
    [
        "$scope",
        "$state",
        "$sce",
        "authService",
        function($scope, $state, $sce, authService)
        {
            $scope.flash = {
                "show":     false,
                "type":     null,
                "message":  null
            };

            $scope.login = function()
            {
                var response = authService.login($scope.login.username, $scope.login.password);

                alert(response);

                if ($scope.login.username == "dev" && $scope.login.password == "dev") {


                    $state.go("/dashboard");
                } else {
                    /*
                     * Set error message
                     */
                    $scope.flash.show = true;
                    $scope.flash.type = "alert-danger";
                    $scope.flash.message = "<strong>Oh snap!</strong> Invalid username or password."

                    /*
                     * Shake login form
                     */
                    $("#login_form").addClass("shake").delay(500).queue(function()
                    {
                        $(this).removeClass("shake").dequeue();
                    });

                    /*
                     * Add error class to panel
                     */
                    $("#login_form").children(2).removeClass("panel-primary").addClass("panel-danger");

                    /*
                     * Add error class to input fields
                     */
                    $("#login_username_group").toggleClass("has-error", true);
                    $("#login_password_group").toggleClass("has-error", true);
                }
            }
        }
    ]
);