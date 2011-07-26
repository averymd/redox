<?php

class Access extends AccessControl {

	function loggedIn() {
		return $this->state->getState('loggedIn');
	}

}
?>
