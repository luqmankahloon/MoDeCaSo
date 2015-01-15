/*
 * UPB-BTHESIS
 * Copyright (C) 2014-2015 Peter Folta. All rights reserved.
 *
 * Project:			UPB-BTHESIS
 * Version:			0.0.1
 *
 * File:            /frontend/js/app/services/http_auth_interceptor.js
 * Created:			2015-01-13
 * Last modified:	2015-01-15
 * Author:			Peter Folta <pfolta@mail.uni-paderborn.de>
 */

services.factory(
    "http_auth_interceptor_service",
    [
        "session_service",
        function(session_service)
        {
            return {
                request: function(config)
                {
                    if (session_service.get("api_key")) {
                        config.headers['X-API-Key'] = session_service.get("api_key");
                    }

                    config.headers['X-Client-Application'] = "MoDeCaSo Web Frontend";

                    return config;
                },

                responseError: function(response)
                {
                    if (response.status == 401) {
                        session_service.clear();

                        session_service.set("login_message", "session_timeout");

                        window.location.reload();
                    }
                }
            }
        }
    ]
);