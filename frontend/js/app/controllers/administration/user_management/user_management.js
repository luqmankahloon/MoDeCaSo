/*
 * UPB-BTHESIS
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:			UPB-BTHESIS
 * Version:			0.0.1
 *
 * File:            /frontend/js/app/controllers/administration/user_management/user_management.js
 * Created:			2014-10-23
 * Last modified:	2014-12-17
 * Author:			Peter Folta <pfolta@mail.uni-paderborn.de>
 */

controllers.controller(
    "user_management_controller",
    [
        "$scope",
        "$rootScope",
        "$http",
        "session_service",
        function($scope, $rootScope, $http, session_service)
        {
            $scope.filter = null;
            $scope.order_predicate = "id";
            $scope.order_reverse = false;

            $scope.flash = {
                "show":     false,
                "type":     null,
                "message":  null
            };

            $scope.get_label_class = function (status)
            {
                switch (status) {
                    case "INACTIVE":
                        return "label-default";
                    case "ACTIVE":
                        return "label-success";
                }
            };

            $scope.load_users = function()
            {
                $http({
                    method:     "get",
                    url:        "/server/administration/user_management/get_user_list",
                    headers:    {
                        "X-API-Key":    session_service.get("api_key")
                    }
                }).then(
                    function(response)
                    {
                        $scope.flash.show = false;

                        $scope.users = response.data.users;
                    },
                    function(response)
                    {
                        $scope.flash.show = true;
                        $scope.flash.type = "alert-danger";
                        $scope.flash.message = "<span class='glyphicon glyphicon-exclamation-sign'></span> <strong>" + get_error_title() + "</strong> Error loading users.";

                        shake_element($("#user_management_flash"));
                    }
                );
            };

            $scope.$on(
                "load_users",
                function(event, args)
                {
                    $scope.load_users();
                }
            );

            $rootScope.$broadcast("load_users");
        }
    ]
);