<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /server/controllers/projects/analysis_results_controller.class.php
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

namespace controllers;

use data\user_roles;
use Exception;
use main\controller;
use model\analysis_results;
use tools\file;

class analysis_results_controller extends controller
{

    public function register_routes()
    {
        $this->app->group(
            "/analysis_results",
            function()
            {
              

                $this->app->get(
                    "/get_analysis_list/:key",
                    array(
                        $this,
                        'get_analysis_list'
                    )
                );

                $this->app->get(
                    "/get_analyser_results/:project_key/:user_name/:model_id",
                    array(
                        $this,
                        'get_analyser_results'
                    )
                );

                $this->app->get(
                    "/export_model/:type/:project_key/:user_name/:model_id",
                    array(
                        $this,
                        'export_model'
                    )
                );
            }
        );


    }

    public function create_model()
    {
        $this->model = new analysis_results();
    }




    public function get_analysis_list($project_key)
    {
        if ($this->auth->authenticate($this->get_api_key(), user_roles::MODERATOR)) {
            $result = $this->model->get_analysis_list($project_key);

            if (!$result['error']) {
                $this->app->render(
                    200,
                    $result
                );
            } else {
                $this->app->render(
                    400,
                    $result
                );
            }
        } else {
            $this->app->render(
                403,
                array(
                    'error'         => true,
                    'msg'           => "insufficient_rights"
                )
            );
        }

    }

 
    public function get_analyser_results($project_key, $user_name, $model_id)
    {
        $this->app->render(
            200,
            $this->model->get_analyser_results($project_key, $user_name, $model_id)
        );
    }
    public function export_model($type, $project_key, $user_name, $model_id)
    { 

            $export = $this->model->export_model($type,$project_key, $user_name, $model_id);
            
            $file = new file($this->app);
            if($type == "JSON"){
                $file->set_filename($project_key."_".$user_name."_".date("Ymdhis").".json");
                $file->set_mimetype("application/json");
                $file->set_file_contents(json_encode($export,JSON_PRETTY_PRINT));
            }
            else{
                $file->set_filename($project_key."_".$user_name."_".date("Ymdhis").".csv");
                $file->set_mimetype("text/csv");
                $file->set_file_contents($export);
            }
            
            $file->serve();
    }

}