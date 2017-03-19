/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /frontend/js/app/controllers/projects/analysis.js
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

controllers.controller(
    "analysis_controller",
    [
        "$scope",
        "$rootScope",
        "$http",
        "$modal",
        "session_service",
        "key",
        "$state",
        function($scope, $rootScope, $http, $modal, session_service, key,$state)
        {
            $scope.key = key;

            $scope.participants_collapse = false;
            $scope.solution_collapse     = true;
            $scope.selectedCategoriesLength = 0;
            $scope.message = "";
            $scope.show_message = true;
            $scope.width = null;

            $scope.status_flash = {
                "show":     false,
                "type":     null,
                "message":  null
            };

            $scope.get_category_status_label_class = function (status)
            {
                //console.log(status);
                if(status)
                        return "label-success";
                else
                        return "label-default";
                
            };

            $scope.get_seed = function()
            {
                for (var i = 0; i < $scope.participants.length; i++) {
                    if ($scope.participants[i].id == $scope.project.seed) {
                        return $scope.participants[i].first_name + " " + $scope.participants[i].last_name;
                    }
                }
            };

            $scope.load_results = function()
            {
                $http({
                    method:     "get",
                    url:        "/server/analysis/get_category/" + $scope.key
                }).then(
                    function(response)
                    {
                        $scope.key                  = response.data.key;
                        $scope.users                  = response.data.users;
                        $scope.categories             = response.data.categories;

                        $scope.selectedCategories = {};
                        //$scope.width=document.getElementById("graph-width").offsetWidth;
                        for(i = 0 ; i < $scope.categories.length; i++) {
                            $scope.selectedCategories[$scope.categories[i]] = false;
                        }
                        //console.log($scope.width);
                        $scope.unSortedKeys = Object.keys($scope.selectedCategories);
                        //console.log($scope.unSortedKeys);
                        $scope.cards             = response.data.cards;
                        $scope.graph_data             = response.data.graph_data;
                        $scope.options = setOptions();
                        $scope.data = dataCollection();
                        //console.log($scope.data);
                        $scope.max_length = 0;
                        $scope.max_length_index = 0;
                        for(i = 0 ; i < $scope.data.length; i++) {
                            if($scope.data[i].values.length > $scope.max_length){
                                $scope.max_length = $scope.data[i].values.length;
                                $scope.max_length_index = i;
                            }
                        }
                        //console.log($scope.max_length_index);
                        $scope.message1          = response.data.message.replace(/\n/gi, "<br>");
                        $scope.message1          =$scope.message1.replace("%first_name%",session_service.get("first_name"));
                        $scope.message1          =$scope.message1.replace("%last_name%",session_service.get("last_name"));
                        $scope.message = $scope.message1;
                        if($scope.show_message){
                            $scope.show_info_message();
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

            function setOptions() { 
                return { 

                    chart: {
                        type: 'lineChart',
                        height: 550,
                        //width: 930,
                        margin : {top: 0, right: 80, bottom: 80, left: 150},
                        //pointRange:[-5,-5,0,5,5],
                        //pointSize:1,
                        x: function(d){ return d.i; },
                        y: function(d){ return d.value; },
                        useInteractiveGuideline: false,
                        dispatch: {
                            elementClick: function(e){ console.log("I am clicked"); },
                            stateChange: function(e){ console.log("stateChange");},
                            changeState: function(e){ console.log("changeState"); },
                            tooltipShow: function(e){ console.log("tooltipShow"); },
                            tooltipHide: function(e){ console.log("tooltipHide"); }
                        },
                        tooltip: {
                            contentGenerator: function(d) {
                                
                              if(typeof($scope.cards[d.series[0].key][d.point.label])  != "undefined") { 
                              var header = 
                                "<thead>" + 
                                  "<tr>" +
                                    "<td class='key'><strong>" + d.point.label + "</strong></td>" +
                                  "</tr>" + 
                                "</thead>";

                              var rows = 
                                "<tr>" +
                                  "<td class='x-value'>" + $scope.cards[d.series[0].key][d.point.label] + "</td>" + 
                                "</tr>" ;
                               

                              
                                
                              return "<table>" +
                                  header +
                                  "<tbody>" + 
                                    rows + 
                                  "</tbody>" +
                                "</table>";}
                                //console.log(d);
                                //console.log($scope.cards[d.series[0].key]);
                                //console.log(d.point.label);
                                //return $scope.cards[d.series[0].key][d.point.label];

                                                                            }
                        },
                        legend: {
                            //updateState: false,
                            //width: 300,

                            dispatch: {
                                legendClick: function(e) {
                                    
                                    $scope.selectedCategories[e.key] = !$scope.selectedCategories[e.key];
                                    if($scope.selectedCategories[e.key])
                                        $scope.selectedCategoriesLength ++;
                                    else
                                        $scope.selectedCategoriesLength --;
                                    //console.log(e);
                                    $scope.$apply();
                                },
                                legendDblclick: function (e) {
                                    
                                    e.preventDefault();
                                    //throw "legendDblclick disabled";

                                }

                            }

                        },
                        
                        xAxis: {
                            axisLabel: 'Users',
                            axisLabelDistance: 0,
                           
                            rotateLabels: -45,
                            ticks: $scope.users.length,
                            tickFormat: function(i){
                                //converting name to F.LastName
                                if (i % 1 == 0){
                                    return $scope.data[$scope.max_length_index].values[i].label.split(' ').slice(0, -1).join(' ').slice(0, 1)+". "+$scope.data[$scope.max_length_index].values[i].label.split(' ').slice(-1).join(' ');
                                }
                                //return false;
                                
                            }
                        },
                        yAxis: {
                            axisLabel: 'Categories',
                            axisLabelDistance: 20,
                            //"rotateLabels": 0,
                            ticks: $scope.categories.length,
                            tickFormat: function(d) {
                                return $scope.categories[d];
                                },
                        },
                        yDomain: [0,$scope.categories.length],
                        callback: function(chart){
                            //console.log("!!! lineChart callback !!!");
                        }
                    },
                    title: {
                        enable: false,
                        text: 'Category Analysis of MODECASO'
                    },
                    subtitle: {
                        enable: false,
                        text: 'Subtitle for simple line chart.',
                        css: {
                            'text-align': 'center',
                            'margin': '10px 13px 0px 7px'
                        }
                    },
                    caption: {
                        enable: false,
                        html: '<b>Figure 1.</b> ',
                        css: {
                            'text-align': 'justify',
                            'margin': '10px 13px 0px 7px'
                        }
                    }
                };    
            }

            function dataCollection() {
                var data = [];

                for(var key in $scope.graph_data){
                    data.push({ values: $scope.graph_data[key], key: key });
                }

                data.forEach(function(obj){
                    obj.values.forEach(function(d,i){
                        d.i = $scope.users.indexOf(d.label);
                        return d;
                    });
                });
               
                return data;
            };
           
                       /* When clicked on a category in Legend of graph.*/
            $scope.back_analysis= function(){

                
                $scope.show_message = false;
                $("#solution-chart").hide();
                $("#analysis-chart").show();
                window.dispatchEvent(new Event('resize'));

                $scope.message = $scope.message1;
                $scope.status_flash = {
                    "show":     false,
                    "type":     null,
                    "message":  null
                };

            }
            $scope.show_solution= function(){
                $("#analysis-chart").hide();
                $("#solution-chart").show();


                $http({
                    method:     "post",
                    url:        "/server/analysis/get_solution/" + $scope.key,
                    data:       {
                        selectedCategories:      $scope.selectedCategories
                    }
                }).then(
                    function(response)
                    {
                        //$("#solution-chart").show();

                        $scope.model_categories       = response.data.model_categories;
                        $scope.unsorted_cards   = response.data.unsorted_cards;

                        console.log($scope.unsorted_cards);
                        console.log($scope.model_categories);

                        $scope.unsorted_cards.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                        $scope.model_categories.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                        for (var i = 0; i < $scope.model_categories.length; i++) {
                            $scope.model_categories[i].cards.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                        }


                        $scope.message2 = response.data.msg.replace(/\n/gi, "<br>");
                        $scope.message2 = $scope.message2.replace("%first_name%",session_service.get("first_name"));
                        $scope.message2 = $scope.message2.replace("%last_name%",session_service.get("last_name"));
                        $scope.message  = $scope.message2;

                        if(response.data.flash_message != ""){
                            $scope.status_flash.show = true;
                            $scope.status_flash.type = "alert-warning";
                            $scope.status_flash.message = "<span class='glyphicon glyphicon-warning-sign'></span> <strong>" + "Suggestion:" + "</strong> <br />All cards of following category(ies)"+response.data.flash_message+"<br /> are in the other selected categories on the basis of the most number of occurrences according to users of this experiment. <br />So you can unselect this(these) category(ies).";
                        }
                        //shake_element($("#project_flash"));
                        if($scope.show_message){
                            $scope.show_info_message();
                        }
                    }
                );
            }
            $scope.$watch(
                function()
                {
                    if(typeof($scope.model_categories)  != "undefined") 
                        return $scope.model_categories.length;
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

            // UiSort
            $scope.sortable_options = {
                "cursor":                   "url('/frontend/cur/closedhand.cur'), move",
                "forcePlaceholderSize":     true,
                "opacity":                  0.8,
                "helper":                   "clone",
                "connectWith":              ".sortable",
                "placeholder":              "card-sortable-highlight",
                "scroll":                   false,
                "appendTo":                 "body",
                "start":                    function(e, ui)
                {

                },
                "stop":                     function(e, ui)
                {
                    $scope.unsorted_cards.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                    $scope.model_categories.sort(sort_by("text", false, function(a){return a.toUpperCase()}));
                    for (var i = 0; i < $scope.model_categories.length; i++) {
                        $scope.model_categories[i].cards.sort(sort_by("text", false, function (a) {
                            return a.toUpperCase()
                        }));
                    }

                }
            };

            $scope.save_final_model = function()
            {
                //console.log($scope.model_categories);

                $http({
                    method:     "post",
                    url:        "/server/analysis/save_final_model/" + $scope.key,
                    data:       {
                        model_categories:     $scope.model_categories
                    }
                }).then(
                    function(response)
                    {
                        $scope.user_name   = response.data.user_name;
                        $scope.thank_you =$modal.open(
                            {
                                templateUrl:    "/frontend/tpl/projects/thank_you.tpl",
                                scope:          $scope,
                                backdrop:       "static"
                            }
                        );
                    }
                );
            };
            $scope.changeState = function () {
                //$scope.$close(true);
                $state.go('/projects/analysis/analysis_results',{project_key: $scope.key, user_name: $scope.user_name});
                };



            $scope.show_info_message = function()
            {
                $scope.welcome = $modal.open(
                    {
                        templateUrl:    "/frontend/tpl/projects/info.tpl",
                        scope:          $scope,
                        backdrop:       "static"
                    }
                );
            }; 
        }

    ]
);