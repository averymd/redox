<?php

/* MAIN CONFIGURATION */

// If the installation is at the root level (http://www.example.com/) leave this blank.
// Otherwise set it to the access path with only a leading slash.
define('FOLDER', '');
//define('FOLDER', '/subsection/othersection');

// Defines the default controller, if unspecified.
define('DEFAULT_CONTROLLER', 'home');

// Enable viewing the profiler (Benchmarks, $_POST, Queries, and Session States & Variables).
define('PROFILER', 1);

// Define whether this is a staging or production environment. (0: staging; 1: production)
define('STATUS', 0);

// Define image path
define('IMAGES', FOLDER.'/public/_images/');

define('CRYPT_SALT', 'tast3e salt!ness f0r a very weer!');

/* LOCALIZATION */

// Define format for human readable time.
define('DATE_HUMAN', 'M jS Y H:i:s');


/*	RUNTIME SERVER CONFIGURATION */
// This is where all commands for configuring the server goes. Especially useful in shared hosting environments.

?>
