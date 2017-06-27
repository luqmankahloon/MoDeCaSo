<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /server/controllers/projects/analysis_controller.class.php
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

namespace controllers;

use data\user_roles;
use Exception;
use main\controller;
use model\analysis;

class analysis_controller extends controller
{

    public function register_routes()
    {
        $this->app->group(
            "/analysis",
            function()
            {
                $this->app->get(
                    "/get_category/:key",
                    array(
                        $this,
                        'get_category'
                    )
                );
                $this->app->post(
                    "/get_solution/:key",
                    array(
                        $this,
                        'get_solution'
                    )
                );
                $this->app->post(
                    "/save_final_model/:key",
                    array(
                        $this,
                        'save_final_model'
                    )
                );
            }
        );

    }

    public function create_model()
    {
        $this->model = new analysis();
    }

   
    public function get_category($project_key)
    {
        if ($this->auth->authenticate($this->get_api_key(), user_roles::MODERATOR)) {
            $result = $this->model->get_category($project_key);

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

    public function get_solution($project_key)
    {
         $selected_categories = $this->request->selectedCategories;

        if ($this->auth->authenticate($this->get_api_key(), user_roles::MODERATOR)) {
            $result = $this->model->get_solution($project_key,$selected_categories);

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
    public function save_final_model($project_key)
    {
         $model_categories = $this->request->model_categories;
         $model_comment = $this->request->model_comment;

        if ($this->auth->authenticate($this->get_api_key(), user_roles::MODERATOR)) {
            $result = $this->model->save_final_model($project_key,$model_categories,$model_comment,$this->get_user_id());
            

            $this->database->select("users", "`username`" , "`id` = '".$this->get_user_id()."'");
        $user = $this->database->result()[0];
        $user_name = $user['username'];

            $this->app->render(
                200,
                array(
                    'error'     => false,
                    'msg'       => "Data saved.",
                    'user_name' => $user_name
                )
            );
        }
    }

}