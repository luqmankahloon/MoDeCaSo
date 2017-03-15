<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /server/model/projects/analysis_results.class.php
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

namespace model;

use data\participant_statuses;
use \Exception;
use data\project_statuses;
use main\config;
use main\database;

class analysis_results
{

    private $config;
    private $database;

    public function __construct()
    {
        $this->config = config::get_instance();
        $this->database = database::get_instance();
    }




    public function get_analysis_list($project_key)
    {  
        $this->database->select("projects", null, "`key` = '".$project_key."'");

        if ($this->database->row_count() == 1) {
            $project = $this->database->result()[0];
            $project_id = $project['id'];
            $this->database->select("experiment_final_models", "`user_id`", "`project` = '".$project_id."'","`user_id`");
            $model_selector = $this->database->result();
            $analysers= [];
            for ($i = 0; $i < count($model_selector); $i++) {
                $this->database->select("users", null, "`id` = '".$model_selector[$i]['user_id']."'");
                array_push($analysers, $this->database->result()[0]);
            }
            $result = array(
                'error'         => false,
                'project'       => $project,
                'analysers'  => $analysers,
            );
        }else {
            /*
             * Invalid project key provided
             */
            $result = array(
                'error'         => true,
                'msg'           => "invalid_username"
            );
        }

        return $result;
      
    }
    public function get_analyser_results($project_key, $user_name)
    {
        $this->database->select("projects", null, "`key` = '".$project_key."'");
        $project = $this->database->result()[0];
        $project_id = $project['id'];

        $this->database->select("users", null, "`username` = '".$user_name."'");
        $user = $this->database->result()[0];
        $user_id = $user['id'];

        /*
         * Get Experiment data
         * Categories
         */
        $this->database->select("experiment_final_models", "`category`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."'", "`category`");
        $categories = $this->database->result();
        //print_r($categories);
        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_final_models", "`card`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `category` = '".$categories[$i]['category']."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                $card = $this->database->result()[0];

                $cards_model[] = $card;
            }

            $categories[$i]['cards'] = $cards_model;
            
            $categories[$i]['text'] = $categories[$i]['category'];
            unset($categories[$i]['category']);
        }
        //print_r($user);

        return array(
            'categories'        => $categories,
            'user'       => $user
        );
    }
    public function export_model($project_key,$user_name)
    {
        $this->database->select("projects", null, "`key` = '".$project_key."'");
        $project = $this->database->result()[0];
        $project_id = $project['id'];

        $this->database->select("users", null, "`username` = '".$user_name."'");
        $user = $this->database->result()[0];
        $user_id = $user['id'];

        $this->database->select("experiment_final_models", "`category`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."'", "`category`");
        $categories = $this->database->result();
        //print_r($categories);
        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_final_models", "`card`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `category` = '".$categories[$i]['category']."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`text`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                $card = $this->database->result()[0];

                $cards_model[] = $card;
            }

            $categories[$i]['cards'] = $cards_model;
            

        }
        //print_r($user);

        return  $categories;
    }





}