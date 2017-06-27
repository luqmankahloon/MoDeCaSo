<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.0
 *
 * File:            /server/model/projects/projects.class.php
 * Created:         2014-11-24
 * Author:          Peter Folta <mail@peterfolta.net>
 */

namespace model;

use data\participant_statuses;
use \Exception;
use data\project_statuses;
use main\config;
use main\database;

class projects
{

    private $config;
    private $database;

    public function __construct()
    {
        $this->config = config::get_instance();
        $this->database = database::get_instance();
    }

    public function create_project($title, $key, $lead)
    {
        /*
         * Check if project key already exists
         */
        $this->database->select("projects", null, "`key` = '".$key."'");

        if ($this->database->row_count() == 0) {

            /*
             * Insert new project into database
             */
            $this->database->insert("projects", array(
                'title'                 => $title,
                'key'                   => $key,
                'lead'                  => $lead,
                'completion'            => $this->config->get_config_value("project", "completion_timestamp"),
                'reminder'              => $this->config->get_config_value("project", "reminder_timestamp"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            /*
             * Project ID
             */
            $project_id = $this->database->get_insert_id();

            /*
             * Insert default messages into database
             */
            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "email_invitation",
                'message'               => $this->config->get_config_value("project", "email_invitation"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "sp_email_invitation",
                'message'               => $this->config->get_config_value("project", "sp_email_invitation"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "welcome_message",
                'message'               => $this->config->get_config_value("project", "welcome_message"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "sp_welcome_message",
                'message'               => $this->config->get_config_value("project", "sp_welcome_message"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "email_reminder",
                'message'               => $this->config->get_config_value("project", "email_reminder"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "email_timeout",
                'message'               => $this->config->get_config_value("project", "email_timeout"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "category_analysis",
                'message'               => $this->config->get_config_value("project", "category_analysis"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));

            $this->database->insert("project_messages", array(
                'project'               => $project_id,
                'type'                  => "suggested_solution",
                'message'               => $this->config->get_config_value("project", "suggested_solution"),
                'created'               => $GLOBALS['timestamp'],
                'last_modified'         => 0
            ));
            
            $result = array(
                'error'         => false,
                'msg'           => "project_created"
            );
        } else {
            $result = array(
                'error'         => true,
                'msg'           => "project_key_already_exists"
            );
        }

        return $result;
    }

    public function delete_project($project_key)
    {
        /*
         * Check if project exists
         */
        $this->database->select("projects", null, "`key` = '".$project_key."'");

        if ($this->database->row_count() == 1) {
            /*
             * Delete project
             */
            $this->database->delete("projects", "`key` = '".$project_key."'");

            $result = array(
                'error'         => false,
                'msg'           => "project_deleted"
            );
        } else {
            /*
             * Invalid project key provided
             */
            $result = array(
                'error'         => true,
                'msg'           => "invalid_project_key",
            );
        }

        return $result;
    }

    public function get_project_list($lead = null)
    {
        if (is_null($lead)) {
            $this->database->select("projects", "`id`, `title`, `key`, `lead`, `status`, `created`, `last_modified`, `started`");
        } else {
            $this->database->select("projects", "`id`, `title`, `key`, `lead`, `status`, `created`, `last_modified`, `started`", "`lead` = '".$lead."'");
        }

        $projects = $this->database->result();

        for ($i = 0; $i < count($projects); $i++) {
            $this->database->select("users", "`username`, `first_name`, `last_name`", "`id` = '".$projects[$i]['lead']."'");
            $lead = $this->database->result()[0];

            $projects[$i]['lead'] = $lead['first_name']." ".$lead['last_name']." (".$lead['username'].")";

            $projects[$i]['status'] = project_statuses::$values[$projects[$i]['status']];
        }

        return $projects;
    }

    public function get_project($project_key)
    {
        $this->database->select("projects", null, "`key` = '".$project_key."'");

        if ($this->database->row_count() == 1) {
            $project = $this->database->result()[0];

            $project_id = $project['id'];

            /*
             * Set Project Lead information
             */
            $this->database->select("users", "`username`, `first_name`, `last_name`", "`id` = '".$project['lead']."'");
            $lead = $this->database->result()[0];

            $project['lead'] = $lead['first_name']." ".$lead['last_name']." (".$lead['username'].")";

            $project['status'] = project_statuses::$values[$project['status']];

            /*
             * Retrieve messages
             */
            $this->database->select("project_messages", null, "`project` = '".$project_id."'");
            $messages = $this->database->result();

            $project_messages = array();

            foreach ($messages as $message) {
                $project_messages[$message['type']] = $message;
            }

            /*
             * Retrieve list of participants
             */
            $this->database->select("project_participants", null, "`project` = '".$project_id."'", null, null, "`order` ASC");
            $project_participants = $this->database->result();

            for ($i = 0; $i < count($project_participants); $i++) {
                $project_participants[$i]['status'] = participant_statuses::$values[$project_participants[$i]['status']];
            }

            /*
             * Retrieve list of cards
             */
            $this->database->select("project_cards", null, "`project` = '".$project_id."'");
            $project_cards = $this->database->result();

            $result = array(
                'error'         => false,
                'project'       => $project,
                'messages'      => $project_messages,
                'participants'  => $project_participants,
                'cards'         => $project_cards
            );
        } else {
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

    public function edit_project($project_key, $completion, $reminder)
    {
        $project_id = self::get_project_id($project_key);

        $this->database->update("projects", "`id` = '".$project_id."'", array(
            'completion'        => $completion,
            'reminder'          => $reminder
        ));

        self::compute_project_status($project_key);
        self::update_last_modified($project_key);

        return array(
            'error'         => false,
            'msg'           => "project_edited"
        );
    }

    /**
     * get_project_id ( )
     *
     * Returns the ID of a project represented by a key
     *
     * @param   string  $project_key    The project key
     * @return  int                     The ID of the associated project
     * @throws  Exception               Invalid project key
     */
    public static function get_project_id($project_key)
    {
        $database = database::get_instance();

        $database->select("projects", "`id`", "`key` = '".$project_key."'");

        if ($database->row_count() == 1) {
            $project_id = $database->result()[0]['id'];

            return $project_id;
        }

        throw new Exception("Invalid project key '".$project_key."'");
    }

    public static function update_last_modified($project_key)
    {
        $database = database::get_instance();

        $database->select("projects", null, "`key` = '".$project_key."'");

        if ($database->row_count() == 1) {
            $database->update("projects", "`key` = '".$project_key."'", array(
                'last_modified' => $GLOBALS['timestamp']
            ));
        } else {
            throw new Exception("Invalid project key '".$project_key."'");
        }
    }

    public static function compute_project_status($project_key)
    {
        $database = database::get_instance();

        $database->select("projects", null, "`key` = '".$project_key."'");

        if ($database->row_count() == 1) {
            $project = $database->result()[0];
            $status  = $project['status'];

            /*
             * Retrieve list of participants
             */
            $database->select("project_participants", null, "`project` = '".$project['id']."'");
            $participant_count = $database->row_count();

            /*
             * Retrieve list of cards
             */
            $database->select("project_cards", null, "`project` = '".$project['id']."'");
            $card_count = $database->row_count();

            switch ($status) {
                case project_statuses::CREATED:
                    if ($participant_count > 0 && $card_count > 0) {
                        $status = project_statuses::READY;
                    }

                    break;
                case project_statuses::READY:
                    if ($participant_count < 1 || $card_count < 1) {
                        $status = project_statuses::CREATED;
                    }

                    break;
            }

            /*
             * Update project status
             */
            $database->update("projects", "`key` = '".$project_key."'", array(
                'status'        => $status
            ));
        } else {
            throw new Exception("Invalid project key '".$project_key."'");
        }
    }

    public function start_project($project_key)
    {
        $project_id = self::get_project_id($project_key);

        /*
         * Check if project ready
         */
        $this->database->select("projects", "status", "`id` = '".$project_id."'");
        $project_status = $this->database->result()[0]['status'];

        if ($project_status == project_statuses::READY) {
            $this->database->update("projects", "`id` = '".$project_id."'", array(
                'status'            => project_statuses::RUNNING,
                'started'           => $GLOBALS['timestamp']
            ));

            self::update_last_modified($project_key);

            return array(
                'error'         => false,
                'msg'           => "project_started"
            );
        } else {
            throw new Exception("Project '".$project_key."' not ready to be started.");
        }
    }

    public function get_results($project_key, $participant)
    {
        $project_id = projects::get_project_id($project_key);

        /*
         * Get Experiment data
         * Categories
         */
        $this->database->select("experiment_categories", "`id`, `text`", "`project` = '".$project_id."' AND `participant` = '".$participant."'");
        $categories = $this->database->result();

        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_models", "`card`", "`project` = '".$project_id."' AND `participant` = '".$participant."' AND `category` = '".$categories[$i]['id']."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                $card = $this->database->result()[0];

                $cards_model[] = $card;
            }

            $categories[$i]['cards'] = $cards_model;
        }

        $this->database->select("project_participants", null, "`id` = '".$participant."'");
        $participant = $this->database->result()[0];

        return array(
            'categories'        => $categories,
            'participant'       => $participant
        );
    }

    public function export_model($type,$project_key, $participant)
    {
        $csv = '"user_id","card_label","card_id","category_label","category_id"'.PHP_EOL;
        
        $project_id = projects::get_project_id($project_key);

        $this->database->select("experiment_categories", "distinct(text)", "`project` = '".$project_id."'");
        $u_categories = $this->database->result();

        /*
         * Get Experiment data
         * Categories
         */
        $this->database->select("experiment_categories", "`id` as `category_id`, `text` as `category_label`", "`project` = '".$project_id."' AND `participant` = '".$participant."'");
        $categories = $this->database->result();

        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_models", "`card`", "`project` = '".$project_id."' AND `participant` = '".$participant."' AND `category` = '".$categories[$i]['category_id']."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`id` as `card_id`, `text` as `card_label`, `tooltip`", "`id` = '".$card_in_category['card']."'");
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

    public function export_project_model($type,$project_key)
    {
        $csv = '"user_id","card_label","card_id","category_label","category_id"'.PHP_EOL;
        
        $project_id = projects::get_project_id($project_key);

        $this->database->select("project_participants", "`id` as `user_id`", " status = ".participant_statuses::COMPLETED." AND `project` = '".$project_id."'", null, null, "`order` ASC");
        $project_participants = $this->database->result();

        //print_r($project_participants);

        $this->database->select("experiment_categories", "distinct(text)", "`project` = '".$project_id."'");
        $u_categories = $this->database->result();
        $j=1;
        /* Getting categories and cards for all users (user by user)*/
        foreach ($project_participants as $participant) 
        {
            $this->database->select("experiment_categories", "`id` as `category_id`, `text` as `category_label`", "`project` = '".$project_id."' AND `participant` = '".$participant['user_id']."'");
            $categories = $this->database->result();
            
            for ($i = 0; $i < count($categories); $i++) 
            {
                /* Get Cards in Category */
                $this->database->select("experiment_models", "`card`", "`project` = '".$project_id."' AND `participant` = '".$participant['user_id']."' AND `category` = '".$categories[$i]['category_id']."'");
                $cards_in_category = $this->database->result();

                $cards_model = array();

                foreach ($cards_in_category as $card_in_category) 
                {
                    $this->database->select("project_cards", "`id` as `card_id`, `text` as `card_label`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                    $card = $this->database->result()[0];

                    $cards_model[] = $card;
                    //$participants_model[$participant['first_name'].' '.$participant['last_name']][$categories[$i]['text']][]=$card;

                   foreach($u_categories as $key => $product)
                    {
                        if ( $product['text'] == $categories[$i]['category_label'] )
                            $temp= $key+1;
                    }
                                        
                    $csv .= '"'.$j.'","'.$card['card_label'].'","'.$card['card_id'].'","'.$categories[$i]['category_label'].'","'.$temp.'"'.PHP_EOL;
                } 
                    $categories[$i]['cards'] = $cards_model;
                 
              
            }
            $participant['user_id'] = $j;
            $JSON[]= array('user' => $participant,
                                         'category' => $categories
                                        );
            $j++;
        }


        if($type == "JSON")
            return  $JSON;
        else
            return $csv;
    }

}