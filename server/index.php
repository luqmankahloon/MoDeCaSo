<?php

/*
 * MoDeCaSo - A Web Application for Modified Delphi Card Sorting Experiments
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:         MoDeCaSo
 * Version:         1.0.0
 *
 * File:            /server/index.php
 * Created:         2014-11-03
 * Author:          Peter Folta <mail@peterfolta.net>
 */

require "vendor/autoload.php";

require "main/config.class.php";
require "main/controller.class.php";
require "main/database.class.php";
require "main/errorhandling.class.php";

require "data/participant_statuses.class.php";
require "data/project_statuses.class.php";
require "data/user_roles.class.php";
require "data/user_statuses.class.php";

require "tools/error.class.php";
require "tools/file.class.php";
require "tools/mail.class.php";
require "tools/url.class.php";

require "controllers/administration/server_maintenance_service_controller.class.php";
require "controllers/administration/user_management_controller.class.php";
require "controllers/experiment/experiment_controller.class.php";
require "controllers/projects/cards_controller.class.php";
require "controllers/projects/messages_controller.class.php";
require "controllers/projects/participants_controller.class.php";
require "controllers/projects/projects_controller.class.php";

// Added by Luqman Ahmad
require "controllers/projects/analysis_controller.class.php";
require "controllers/projects/analysis_results_controller.class.php";

require "controllers/auth_controller.class.php";

require "model/administration/server_maintenance_service.class.php";
require "model/administration/user_management.class.php";
require "model/experiment/experiment.class.php";
require "model/projects/cards.class.php";

// Added by Luqman Ahmad
require "model/projects/analysis.class.php";
require "model/projects/analysis_results.class.php";

require "model/projects/messages.class.php";
require "model/projects/participants.class.php";
require "model/projects/projects.class.php";
require "model/auth.class.php";

use controllers\experiment_controller;
use \Slim\Slim;

use main\config;
use main\database;
use main\errorhandling;

use controllers\auth_controller;
use controllers\server_maintenance_service_controller;
use controllers\user_management_controller;
use controllers\projects_controller;

// Added by Luqman Ahmad
use controllers\analysis_controller;
use controllers\analysis_results_controller;

use controllers\cards_controller;
use controllers\participants_controller;
use controllers\messages_controller;

use tools\url;

try {
    /*
     * Initialize global timestamp variable
     */
    $timestamp = time();

    /*
     * Create new instance of web application
     */
    $app = new Slim();

    $json_api_view = new JsonApiView();
    $json_api_view->encodingOptions = JSON_NUMERIC_CHECK;

    $app->view($json_api_view);
    $app->add(new JsonApiMiddleware());

    /*
     * Register custom modified error handler
     */
    $app->error(function (Exception $exception) use ($app) {
        $app->render(500, array(
            'error'     => true,
            'msg'       => "application_error",
            'reason'    => $exception->getMessage(),
            'status'    => 500
        ));
    });

    /*
     * Initialize config object, load and parse config file
     */
    $config = config::get_instance();
    $config->load_config("config.ini");

    /*
     * Check if SSL/TLS requirements are met
     */
    if ($config->get_config_value("main", "require_ssl") && !url::is_https()) {
        throw new Exception("Connection via SSL/TLS required.");
    }

    /*
     * Set debug options
     */
    if ($config->get_config_value("main", "debug")) {
        errorhandling::set_error_handling(true);
        $app->config("debug", false);
    } else {
        errorhandling::set_error_handling(false);
        $app->config("debug", false);
    }

    /*
     * Initialize database object, connect with credentials provided in config file
     */
    $database = database::get_instance();
    $database->connect(
        $config->get_config_value("database", "server"),
        $config->get_config_value("database", "username"),
        $config->get_config_value("database", "password"),
        $config->get_config_value("database", "database")
    );

    /*
     * Set database connection character set
     */
    $database->set_charset("UTF8");

    /*
     * Instantiate controllers
     */
    new auth_controller();
    new server_maintenance_service_controller();
    new user_management_controller();
    new experiment_controller();
    new projects_controller();
    
    // Added by Luqman Ahmad
    new analysis_controller();
    new analysis_results_controller();
    
    new cards_controller();
    new participants_controller();
    new messages_controller();

    /*
     * Finally, handle requests
     */
    $app->run();
} catch (Exception $exception) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
    header("Content-Type: application/json");

    $error = array(
        'error'     => true,
        'msg'       => "application_error",
        'reason'    => $exception->getMessage(),
        'status'    => 500
    );

    print json_encode($error, JSON_NUMERIC_CHECK);
    exit;
}