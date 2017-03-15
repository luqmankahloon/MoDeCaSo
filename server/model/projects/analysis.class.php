<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments and Analysis
 * Copyright (C) 2014-2017 Peter Folta - Luqman Ahmad. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.1
 *
 * File:            /server/model/projects/analysis.class.php
 * Created:         2017
 * Author:          Luqman Ahmad <luqmankahloon@gmail.com>
 */

namespace model;

use data\participant_statuses;
use \Exception;
use data\project_statuses;
use main\config;
use main\database;

class analysis
{

    private $config;
    private $database;

    public function __construct()
    {
        $this->config = config::get_instance();
        $this->database = database::get_instance();
    }

    public function get_category($project_key)
    {

        /* Checking if project key exists */
        $this->database->select("projects", null, "`key` = '".$project_key."'");

        if ($this->database->row_count() == 1) 
        {
            /* Getting project_id */
            $project = $this->database->result()[0];
            $project_id = $project['id'];
            
            /* Retrieve list of participants with all information*/
            $project_participants = $this->get_project_participants($project_id);
            
            /* Getting categories and cards (model) for all users (user by user)*/
            $participants_model = $this->get_participants_model($project_participants,$project_id);


            $i=0; 
            $previous_participant = [];
            foreach ($participants_model as $p_id=>$current_participant) {
                $final_users[$p_id] = [];              
                if($i==0){
                    $previous_participant = $current_participant;
                    $i++; 
                }
                foreach($current_participant as $c_cat=>$c_cards){
                    $final_users[$p_id] = array_merge($final_users[$p_id], $this->find_matches($c_cat,$c_cards,$previous_participant));
                }
                $previous_participant = $current_participant;
            }
            
            //$final_users = array_slice($final_users, 1); 
            //print_r($final_users);
            foreach($final_users as $user_id => $user_cats){
                $user_categories=array_keys($user_cats); 
                $same_cat = [];
                foreach ($user_cats as $cat => $linked_cat) {
                    $final_cat[$cat][]=$user_id;//array($user_id,$cat);
                    $count_value=0;
                    $selected_cat='';

                    foreach ($linked_cat as $lc_cat => $lc_cards) {
                        if( $lc_cat == $cat){
                            $same_cat[] = $selected_cat = $lc_cat;
                            break;
                        }
                        else{ 
                            if(in_array($lc_cat, $same_cat)){
                                continue;
                            }
                            $cc = count($lc_cards);
                            if($cc > $count_value){
                                    $count_value = $cc;
                                    $selected_cat = $lc_cat;
                            }
                        }                            
                    }  
                    if($selected_cat!=$cat && $selected_cat){
                        $final_cat[$selected_cat][]=$user_id;
                        $super_cat[$user_id][$selected_cat] = $cat;

                    }
                }
                
            }
            
            $final_cat=array_map("array_unique",$final_cat);
            
            //print_r($this->calculate_cards($final_users));
            //print_r($super_cat);
            $cat_fix_ids = (array_keys($final_cat));
            foreach ($final_cat as $value => $all_label) {
                $j=0;
                foreach ($all_label as $label) {
                   if(array_key_exists($label, $super_cat)){
                        if(array_key_exists($value, $super_cat[$label])){

                            $y_axis = array_search($super_cat[$label][$value],$cat_fix_ids);
                        }
                        else{
                            $y_axis = array_search($value,$cat_fix_ids) ;
                        }
                    }
                    else{
                        $y_axis = array_search($value,$cat_fix_ids) ;
                    }
                    $graph_data[$value][$j] = array("label"=>$label, "value" => $y_axis);                   
                    $j++;
                }
            }

            $result = array(
                'error'         => false,
                'key'           => $project_key,
                'users'         => array_keys($final_users),
                'categories'    => array_keys($final_cat) ,
                'cards'         => $this->calculate_cards($final_users) ,
                'graph_data'    => $graph_data,
                'message'       => $this->get_project_messages($project_id,"category_analysis"),
                );
        }   
        else
        {
            /* Invalid project key provided */
            $result = array(
                'error'         => true,
                'msg'           => "invalid_username"
            );
        }

        return $result;
    }
    public function get_solution($project_key,$selected_categories)
    {

        /* Checking if project key exists */
        $this->database->select("projects", null, "`key` = '".$project_key."'");

        if ($this->database->row_count() == 1) 
        {
            /* Getting project_id */
            $project = $this->database->result()[0];
            $project_id = $project['id'];

            /* Extract list of selected Categories */
            $true_selected_cat = [];
            foreach ($selected_categories as $key=>$value) {
                if($value == "true") {
                    $true_selected_cat[] = $key;
                }
            }

            /* Retrieve list of participants with all information*/
            $project_participants = $this->get_project_participants($project_id);
            
            /* Getting categories and cards (model) for all users (user by user)*/
            $participants_model = $this->get_participants_model($project_participants,$project_id);
            
            $all_model_frequency = [];
            foreach($participants_model as $user => $user_model) {
                foreach ($user_model as $cat => $cards) {
                    
                    foreach ($cards as $card_key => $card_name) {                        
                        if(!isset($all_model_frequency[$cat][$card_name])) {
                            $all_model_frequency[$cat][$card_name]=0;
                        }
                        $all_model_frequency[$cat][$card_name]++;                                
                    }
                                        
                }               
            }

            //print_r($all_model_frequency);
            $model_frequency = [];
            foreach($participants_model as $user => $user_model) {
                foreach ($user_model as $cat => $cards) {
                    if(in_array($cat, $true_selected_cat)) {
                        foreach ($cards as $card_key => $card_name) {                        
                            if(!isset($model_frequency[$cat][$card_name])) {
                                $model_frequency[$cat][$card_name]=0;
                            }
                            $model_frequency[$cat][$card_name]++;                                
                        }
                    }                     
                }               
            }
            //print_r($model_frequency); 
 
            /* Getting Final model to show in suggested solution */
            $final_model_frequency = [];
            $processed_cards = [];
            foreach ($model_frequency as $cat_frequency=>$current_card_frequency) {                                     
                foreach($current_card_frequency as $current_cards=>$current_frequency) {             
                    $temp = $this->get_compared_item($model_frequency,$cat_frequency,$current_cards);
                    //$final_model_frequency[$temp['cat']][$temp['card']] = $temp['freq']; 
                    if (!in_array($temp['card'], $processed_cards)) {
                        array_push($processed_cards,$temp['card'] );
                        $final_model_frequency[$temp['cat']][$temp['card']] = $temp['freq']; 
                    }else{
                        
                        if(!array_key_exists($temp['cat'],$final_model_frequency))
                            $final_model_frequency[$temp['cat']] = [];
                    }
   
                }       
            } 
             
            //print_r($final_model_frequency);
            
            $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`project` = '".$project_id."'");
            $cards = $this->database->result();

            
            /* Geting categories data of model frequency */
            $model_categories = [];
            $i = 0;
            foreach ($final_model_frequency as $cat_frequency=>$current_card_frequency) {                                     
                
                
                $this->database->select("experiment_categories", "`id`, `text`", "`project` = '".$project_id."' AND `text` = '".$cat_frequency."'");
                //print_r($this->database->result());
                array_push($model_categories, $this->database->result()[0]);
                $cards_model = array();
                foreach($current_card_frequency as $current_cards=>$current_frequency) {             
                    $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`project` = '".$project_id."' AND `text` = '".$current_cards."'");
                    $card = $this->database->result()[0];
                    for ($j = 0; $j < count($cards); $j++) {

                        if ($cards[$j]['id'] == $card['id']) {
                             
                            array_splice($cards, $j, 1);
                    }
                }
                // putting card info to card
                $info="";
                $card['info'] = "";
                foreach ($all_model_frequency as $cat_frequency=>$current_card_frequency) {                                     
                    foreach($current_card_frequency as $current_cards=>$current_frequency) {             
                        if($card['text'] == $current_cards){
                            $info .= $cat_frequency." : ".$current_frequency."<br />";
                        }
       
                    }       
                } 
                $card['info'] = $info;           
                    $cards_model[] = $card;
                    //print_r($card);
                    //array_push($model_categories[$i]['cards'], $this->database->result()[0]);
                }
                $model_categories[$i]['cards'] = $cards_model;
                $i++;       
            } 

            for ($j = 0; $j < count($cards); $j++) {
                $info="";
                $cards[$j]['info'] = "";
                foreach ($all_model_frequency as $cat_frequency=>$current_card_frequency) {                                     
                    foreach($current_card_frequency as $current_cards=>$current_frequency) {             
                        if($cards[$j]['text'] == $current_cards){
                            $info .= $cat_frequency." : ".$current_frequency."<br />";
                        }
       
                    }       
                }

                $cards[$j]['info'] = $info;
            }
            $flash_message="";
            foreach ($model_categories as $m_cat) {                                     

                    if(empty($m_cat['cards'])){
                        $flash_message .= "<br />".$m_cat['text'];
                    }
      
            }

//print_r($cards);
            //print_r($model_categories);
            $result = array(
                'error'             => false,
                'project'           => $project,
                'msg'               => $this->get_project_messages($project_id,"suggested_solution"),
                'model_categories'  => $model_categories ,
                'unsorted_cards'    => $cards,
                'flash_message'    => $flash_message
                );
        }   
        else
        {
            /* Invalid project key provided */
            $result = array(
                                'error'         => true,
                                'msg'           => "invalid_username"
                            );
        }

        return $result;
    }

    private function get_project_messages($project_id,$message_type)
    {  
        $this->database->select("project_messages", null, "`project` = '".$project_id."' AND `type` = '".$message_type."'");
        $message = $this->database->result()[0]['message'];
        //print_r($message);
        /* Replace custom variables */
        //$message = str_replace("%first_name%", $participant['first_name'], $message);
        //$message = str_replace("%last_name%", $participant['last_name'], $message);
        //$message = str_replace("%completion_timestamp%", date("n/j/Y g:i:s A", $project['completion']), $message);
        return $message;
    }

    private function get_compared_item($model_frequency,$compare_cat,$compare_item)
    {
        $temp = [];
        $selection = false;
        foreach ($model_frequency as $cat_frequency=>$current_card_frequency) 
        {                                     
            foreach($current_card_frequency as $current_cards=>$current_frequency)
            {
                if($current_cards == $compare_item && $current_frequency > $model_frequency[$compare_cat][$compare_item]) 
                {            
                    $temp = [];
                    $selection = true;
                    $temp['cat'] =$cat_frequency;
                    $temp['card']= $current_cards;
                    $temp['freq']=$current_frequency;
                }

            }       
        }
        if($selection == false)
        {
            $temp['cat'] =$compare_cat;
            $temp['card']= $compare_item;
            $temp['freq']=$model_frequency[$compare_cat][$compare_item];
        } 
        return $temp;
    }

    /* Retrieve list of participants with all information*/
    private function get_project_participants($project_id)
    {
        $this->database->select("project_participants", null, " status = ".participant_statuses::COMPLETED." AND `project` = '".$project_id."'", null, null, "`order` ASC");
        $project_participants = $this->database->result();
        for ($i = 0; $i < count($project_participants); $i++) 
        {
             $project_participants[$i]['status'] = participant_statuses::$values[$project_participants[$i]['status']];
        }
        return $project_participants;
    }
    private function get_participants_model($project_participants,$project_id)
    {
        /* Getting categories and cards for all users (user by user)*/
        foreach ($project_participants as $participant) 
        {
            $this->database->select("experiment_categories", "`id`, `text`", "`project` = '".$project_id."' AND `participant` = '".$participant['id']."'");
            $categories = $this->database->result();
           
            for ($i = 0; $i < count($categories); $i++) 
            {
                /* Get Cards in Category */
                $this->database->select("experiment_models", "`card`", "`project` = '".$project_id."' AND `participant` = '".$participant['id']."' AND `category` = '".$categories[$i]['id']."'");
                $cards_in_category = $this->database->result();

                $cards_model = array();

                foreach ($cards_in_category as $card_in_category) 
                {
                    $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                    $card = $this->database->result()[0];

                    $cards_model[] = $card;
                    $participants_model[$participant['first_name'].' '.$participant['last_name']][$categories[$i]['text']][]=$card['text'];
                }
                $categories[$i]['cards'] = $cards_model;
            }
        }
        return $participants_model;
    }
    private function get_project_model($project_id)
    {
        $this->database->select("experiment_categories", "`id`, `text`", "`project` = '".$project_id."'");
        $categories = $this->database->result();

        for ($i = 0; $i < count($categories); $i++) {
            /*
             * Get Cards in Category
             */
            $this->database->select("experiment_models", "`card`", "`project` = '".$project_id."' AND `category` = '".$categories[$i]['id']."'");
            $cards_in_category = $this->database->result();

            $cards_model = array();

            foreach ($cards_in_category as $card_in_category) {
                $this->database->select("project_cards", "`id`, `text`, `tooltip`", "`id` = '".$card_in_category['card']."'");
                $card = $this->database->result()[0];

                $cards_model[] = $card;
            }

            $categories[$i]['cards'] = $cards_model;
        }
        return $categories;
    }


    
    private function find_matches($c_cat,$cards,$pre_user){
        $matches=[];
        foreach($pre_user as $p_cat=>$p_cards){
            if(count($temp = array_values(array_intersect($cards,$p_cards))) > 0 ){
                $matches[$c_cat][$p_cat] = $temp ;
            }
        }
        return $matches;
    }
     private function calculate_cards($final_users){
        foreach($final_users as $user_id => $user_cats){
                $user_categories=array_keys($user_cats); 
                $same_cat = [];
                foreach ($user_cats as $cat => $linked_cat) {
                    $final_cat[$cat][]=$user_id;//array($user_id,$cat);
                    $count_value=0;
                    $selected_cat='';

                    foreach ($linked_cat as $lc_cat => $lc_cards) {
                        
                        if( $lc_cat == $cat){
                            $same_cat[] = $selected_cat = $lc_cat;
                            $count_value = count($lc_cards);
                            break;
                        }
                        else{ 
                            if(in_array($lc_cat, $same_cat)){
                                continue;
                            }
                            $cc = count($lc_cards);
                            if($cc > $count_value){
                                    $count_value = $cc;
                                    $selected_cat = $lc_cat;
                            }
                        }                            
                    }  
                    //if($selected_cat!=$cat && $selected_cat){
                        //$final_cat[$selected_cat][]=$user_id;
                        $super_cat[$cat][$user_id] = "$selected_cat ($count_value)";

                    //}
                }
                
            }
            //print_r($super_cat);
            return $super_cat;
    }

    public function save_final_model($project_key, $data,$user_id)
    {

        $project_id = projects::get_project_id($project_key);

        //$this->database->delete("experiment_final_models", "`project` = '".$project_id."' AND `user_id` = '".$user_id."'");

        foreach ($data as $category) {
            $category_text = $category->text;

            foreach ($category->cards as $card) {
                $card_id = $card->id;

                $this->database->insert("experiment_final_models", array(
                    'project'       => $project_id,
                    'user_id'       => $user_id,
                    'category'      => $category_text,
                    'card'          => $card_id,
                    'created'       => $GLOBALS['timestamp']
                ));

            }
        }
        return true;
    }
}