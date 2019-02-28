<?php

use go1\rest\RestService;
use go1\rest\wrapper\Manifest;

return call_user_func(
    function () {
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', dirname(__DIR__));
        }

        require_once APP_ROOT . '/vendor/autoload.php';

        # Get service configuration from:
        #   - config.php — option to provide custom configuration for local machine.
        #   - config.default.php if above file is not found.
        #   - build from ServiceConfigBuilder — local dev environment.
        $cnf[] = APP_ROOT . '/resources/config.php';
        $cnf[] = APP_ROOT . '/resources/config.default.php';
        $cnf = is_file($cnf[0]) ? $cnf[0] : (is_file($cnf[1]) ? $cnf[1] : null);
        $cnf = !$cnf ? null : require $cnf;
        $cnf = $cnf ?: call_user_func(
            function () {
                /** @var Manifest $cnf */
                $cnf = require __DIR__ . '/../manifest.php';
                $cnf = $cnf->service()->build();

                return $cnf;
            }
        );

        $app = new RestService($cnf);

        return ('cli' === php_sapi_name()) ? $app : $app->run();
    }
);