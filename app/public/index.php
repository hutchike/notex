<?php
// Copyright (c) 2009 Guanoo, Inc.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Lesser General Public License for more details.

$__YAWF_start_time__ = microtime(TRUE);

// Classes should extend YAWF for hooks

class YAWF // Yet Another Web Framework
{
    private static $hooks = array();

    // Run YAWF on a web request

    public static function run()
    {
        self::hook('default', 'self::unknown'); // Set the default hook method
        error_reporting(E_ALL | E_STRICT);      // Report all errors (strict)
        ini_set('include_path', 'app:yawf:.');  // Prefer your app then YAWF
        require_once('lib/utils.php');          // Include utility functions

        $uri = array_key($_SERVER, 'REQUEST_URI'); // Testing is built right in
        $app_class = preg_match('/_test($|[^\w])/', $uri) ? 'App_test' : 'App';
        require_once "lib/$app_class.php";      // ...by subclassing the "App".
        $app = new $app_class();                // 1) Create the Application
        $controller = $app->new_controller();   // 2) Create the Controller

        try { echo $controller->render(); }     // 3) Try to render the View
        catch (Exception $e) { self::handle_exception($app, $e); }
        if (isset($php_errormsg)) $app->add_error_message($php_errormsg);
        $controller->report_errors();           // ...and report any errors.
    }

    // Handle an exception by displaying or redirecting

    protected static function handle_exception($app, $e)
    {
        $error_message = nl2br($e);
        if (ini_get('display_errors')) echo $error_message;
        elseif (EXCEPTION_REDIRECT) header('Location: ' . EXCEPTION_REDIRECT);
        $app->add_error_message($error_message);
    }

    // Throw an "Unknown method" exception

    public static function unknown($name, $args)
    {
        throw new Exception('Unknown method ' . $name . '() called');
    }

    // Hook a method name to some other method

    public static function hook($name, $method)
    {
        self::$hooks[$name] = $method;
    }

    // Catch all undefined methods calls

    public function __call($name, $args)
    {
        // Look for a hooked method to call

        $method = array_key(self::$hooks, $name);
        if (!$method) $method = array_key(self::$hooks, 'default');
        if ($method === 'return') return; // optimization
        elseif ($method) eval("$method(\$name, \$args);");
    }

    // Write benchmark info in the log file

    public static function benchmark($info)
    {
        if (!BENCHMARKING_ON) return;
        global $__YAWF_start_time__; // Compute benchmark times in milliseconds
        $msecs = (int)( 1000 * ( microtime(TRUE) - $__YAWF_start_time__ ) );
        Log::alert($info . " after $msecs ms"); // "Log" helper loaded by run()
    }
}

// Run YAWF!

chdir('../..');
YAWF::run();

// Benchmark

YAWF::benchmark('Rendered ' . $_SERVER['REQUEST_URI']);

// End of index.php
