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
            $this->database->select("experiment_analysis", NULL, "`project` = '".$project_id."'");
            $model_selector = $this->database->result();
            $analysers= [];
            for ($i = 0; $i < count($model_selector); $i++) {
                $this->database->select("users", null, "`id` = '".$model_selector[$i]['user_id']."'");
                $model_selector[$i]['user_data'] = $this->database->result()[0];
                //array_push($analysers, $this->database->result()[0]);
            }
            $result = array(
                'error'         => false,
                'project'       => $project,
                'analysers'  => $model_selector,
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
    public function get_analyser_results($project_key, $user_name, $model_id)
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
        $this->database->select("experiment_final_models", "`category`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `analysis_id` = '".$model_id."'", "`category`");
        $categories = $this->database->result();
        //print_r($categories);
        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_final_models", "`card`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `category` = '".$categories[$i]['category']."' AND `analysis_id` = '".$model_id."'");
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
            'user'       => $user,
            'analysis_id'       => $model_id
        );
    }
    public function export_model($type,$project_key,$user_name, $model_id)
    {
        
        $csv = '"user_id","card_label","card_id","category_label","category_id"'.PHP_EOL;
        $this->database->select("projects", null, "`key` = '".$project_key."'");
        $project = $this->database->result()[0];
        $project_id = $project['id'];

        $this->database->select("users", null, "`username` = '".$user_name."'");
        $user = $this->database->result()[0];
        $user_id = $user['id'];

        $this->database->select("experiment_categories", "distinct(text)", "`project` = '".$project_id."'");
        $u_categories = $this->database->result();

        $this->database->select("experiment_final_models", "`id` as `category_id`,`category` as `category_label`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `analysis_id` = '".$model_id."'", "`category`");
        $categories = $this->database->result();
        //print_r($categories);

        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_final_models", "`card`", "`project` = '".$project_id."' AND `user_id` = '".$user_id."' AND `category` = '".$categories[$i]['category_label']."' AND `analysis_id` = '".$model_id."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`id` as `card_id`,`text` as `card_label`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                $card = $this->database->result()[0];

                $cards_model[] = $card;
                foreach($u_categories as $key => $product)
                {
                    if ( $product['text'] == $categories[$i]['category_label'] )
                        $temp= $key+1;
                }
                $csv .= '"1","'.$card['card_label'].'","'.$card['card_id'].'","'.$categories[$i]['category_label'].'","'.$temp.'"'.PHP_EOL;

            }

            $categories[$i]['cards'] = $cards_model;
            

        }
        $JSON[]= array('user' => array('user_id' => 1),
                 'category' => $categories
                );
        if($type == "JSON")
            return  $JSON;
        else
            return $csv;
    }





}