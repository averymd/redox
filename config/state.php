<?php

/* DEFAULT STATE CONFIGURATION */
$state->addState('loggedIn');
$state->set('loggedIn', false);

$state->setVar('user_ID', 0);
$state->setVar('username', '');

?>