<?php
/***********************************************************************
*                            StateMachine
* Function index:
* 		StateMachine() - constructor
* 
* 	::Session vars::
* 		setVar(name, object, *lifespan*) - sets a session var
* 		unsetVat(name) - unsets a session var
* 		removeVar(name) - removes the var completely
* 		getVar(name) - returns the session var
* 
* 	::States::
* 		setAll() - used as an initializer when all the first rules are added
* 		addState(name) - add a state to the array
* 		addRuleToState(state, string for function) - adds a rule to a state
* 		check(name) - used to return the value of a state
* 		set(name, value, true for silent) - set a state true or false
* 		setStateByRule(name) - use the stored rule to set the state
* 		goBack() - goes back one committed history object
* 		goForward() - goes forward one committed history object
* 		commit(irreversible? default: false) - used to commit a set of changes to a history object, set true for irreversible
* 		getState(name) - returns state true/false only if it is set
* 
* 	::IO::
* 		returnTF(name) - returns the string True or False based on the state
* 		outputStates() - dump of the states array (debug)
* 		outputHistory() - dump of history array (debug)
* 
* Examples of use:
* 	$sm = new StateMachine();
* 	$sm->addState("loggedIn");
* 	$sm->addRuleToState("loggedIn",'return ("yar"=="zomg");');
* 	$sm->setAll();
* 	$sm->setState("loggedIn", true);
* 	$sm->commit();
* 	$sm->setState("loggedIn", false);
* 	$sm->commit(true);
* 	$sm->setState("loggedIn", true);
* 	$sm->commit();
* 	$sm->checkState("loggedIn");
* 	$sm->checkState("!loggedIn");
* 
* 	-This example creates a state and adds a rule to it. The loggedIn state is then changed
* 	 a number of times and committed to history objects 3 times. Finally the state is checked
* 	 in both its standard form and its not form. checkState("loggedIn") will return true and
* 	 checkState("!loggedIn") will return false.
* 
* 	$sm->setVar("uid", 3);
* 	$sm->getVar("uid");
* 
* 	-sets a session variable and returns it.
* 
* 	$sm->addState("woot");
* 	$sm->addRuleToState("woot",'return false');
* 	$sm->evalState("woot"); OR $sm->checkState("woot");
* 	
* 	-if you create a state on the fly (after the setAll()) you can use eval state, but just doing checkState will work
* 
* 	$sm->checkState("woot !name !loggedIn");
* 
* 	-checkState() can take a number of states separated by spaces with nots included
* 
* 	
***********************************************************************/
class StateMachine {
	/*
	* 
	* 
	*	states array has two dimensions
	* 	state["loggedIn"][0] = stateObject class
	* 	state["loggedIn"][1] = its current value
	*/

	var $states = array();
	var $history = array();
	var $curHistory;
	var $historyPos = 1;
	var $session = array();
	var $flashJustSet;

	//constructor
	function StateMachine() {
		$this->curHistory = new historyObject();
		$this->flashJustSet = 0;
	}
	
	function resetAll() {
		foreach($this->session as $key=>$value) {
			$this->session[$key][0] = null;
		}
		foreach($this->states as $key=>$value) {
			$this->states[$key][1] = false;
		}
		
	}
	
	/***********************************************************************
	*                            Session Vars
	***********************************************************************/
	
	function setVar($name, $obj, $lifespan = 'full')
	{
		
		$this->session[$name][0] = $obj;
		$this->session[$name][1] = $lifespan;
		
	}
	
	function unsetVar($name)
	{
		
		$this->session[$name][0] = null;
		$this->session[$name][1] = null;
		
	}
	
	function removeVar($name)
	{
		
		array_splice($this->session, $name, 1);
		
	}
	
	function getVar($name)
	{
		
		if (isset($this->session[$name]))
			return $this->session[$name][0];
		else
			return false;
	}
	
	/***********************************************************************
	*                            States
	* functions: setState, getState, check, setAll, addState
	***********************************************************************/
	
	function addState($name) {
		
		$this->states[$name][0] = new stateObject();
		$this->states[$name][1] = '';
		
	}
	
	function addRuleToState($name, $func) {
		
		$this->states[$name][0]->addRule($func);
		
	}
	
	function toggle($name) {
		
		if($this->states[$name][1])
			$this->setState($name, false);
		else
			$this->setState($name, true);
		
	}
	
	function set($name, $value, $silent = false) {
		
		$this->states[$name][1] = $value;
		if(!$silent)
			$this->curHistory->addChange($name,$value);
		
	}
	
	function setStateByRule($name, $silent = false) {
		
		if(isset($this->states[$name][0])) {
			$value = $this->states[$name][0]->runRules();
			$this->states[$name][1] = $value;
			if(!$silent)
				$this->curHistory->addChange($name,$value);
		} else {
			$this->states[$name][1] = false;
			if(!$silent)
				$this->curHistory->addChange($name,false);
		}
		
	}
	
	
	function getState($name) {
		if(isset($this->states[$name])) {
			if($this->states[$name][1] == '') {
				$this->setStateByRule($name);
			}
			if($this->states[$name][1])
				return true;
			else
				return false;
		}
	}
	
	
	function setAll() {
		
		foreach($this->states as $key=>$val) {
			$this->setStateByRule($key);
		}
		
	}
	
	function check($name) {
		
		if(strpos($name, ' ') === false) {
			if(strpos($name, '!') === false) {
				return $this->getState($name);
			}else {
				$name = substr($name, 1, strlen($name)-1);
				return (!$this->getState($name));
			}
		}else {
			$s = explode(' ',$name);
			foreach($s as $val) {
				if(strpos($val, '!') === false) {	
					if(!$this->getState($val))
						return false;
				}else {	
					$val = substr($val, 1, strlen($val)-1);
					if($this->getState($val))
						return false;
				}
			}
			return true;
		}
	}
	
	
	/***********************************************************************
	*                            History
	***********************************************************************/
	
	function goBack() {
		
		if($this->history[$this->historyPos-1]->reversible)
		{
			if($this->historyPos <= count($this->history))
			{
				if($this->curHistory->num() == 0) {
					$newhistory = $this->history[$this->historyPos];
					foreach($newhistory->changes as $key=>$value) {
						$name = $newhistory->changes[$key][0];
						$value = $newhistory->changes[$key][1];
						$this->setState($name,$value, true);
					}
					if($this->historyPos < count($this->history))
						$this->historyPos++;
				}else {
					foreach($this->history[$this->historyPos-1]->changes as $key=>$value) {
						$name = $this->history[$this->historyPos-1]->changes[$key][0];
						$value = $this->history[$this->historyPos-1]->changes[$key][1];
						$this->setState($name,$value, true);
					}
					$this->curHistory = new historyObject();
				}
			}
		}
		
	}
	
	function goForward() {
		
		if($this->historyPos > 1) {
			$newhistory = $this->history[$this->historyPos-2];
			foreach($newhistory->changes as $key=>$value) {
				$name = $newhistory->changes[$key][0];
				$value = $newhistory->changes[$key][1];
				$this->setState($name,$value, true);
			}
			$this->historyPos--;
		}
		
	}
	
	function irreversibleCommit() {
		
		$this->curHistory = new historyObject();
		$this->curHistory->setIrreversible();
		$this->addStatesToCurHistory();
		$this->commit();
	}
	
	function addStatesToCurHistory() {
		
		foreach($this->states as $key=>$value) {
			//if($this->curHistory->changes[][0]!=$key)
				$this->curHistory->addChange($key,$this->states[$key][1]);
		}
		
	}

	function commit($irreversible = false)
	{
		
		foreach($this->session as $key=>$value)
		{
			if($this->session[$key][1] == 'commit')
				$this->removeVar($key);
		}
		$this->historyPos = 1;
		if(!$irreversible)
		{
			if(count($this->curHistory->changes)!=0)
			{
				$this->curHistory->commit();
				if(count($this->history) < 10000) {
					array_unshift($this->history, $this->curHistory);	
				}else {
					array_shift($this->history);
					array_unshift($this->history, $this->curHistory);
				}
				$this->curHistory = new historyObject();
				$this->curHistory->reversible = true;
			}
		}
		else
			$this->irreversibleCommit();
		
	}
	
	/***********************************************************************
	*                            IO
	***********************************************************************/
	function returnTF($name) {
		
		if($this->getState($name))
			return "true";
		else
			return "false";
	}
	
	function outputHistory(){
		
		$s = "";
		foreach($this->history as $key=>$value){
			$s .= $this->history[$key]->toString();
		}
		$s .="<br>curHistory: ".$this->curHistory->toString();
		return $s;
	}
	
	function outputStates() {
		
		$out = "";
		foreach($this->states as $key=>$value) {
			if($this->states[$key][1] == false) {
				$out.=$key.": "."false<br/>";
			}else {
				$out.=$key.": "."true<br/>";
			}
		}
		return $out;
	}
	
}

/***********************************************************************
*                            History Object
***********************************************************************/

class historyObject {
	
	var $changes = array();
	var $commit = false;
	var $reversible = true;

	
	function stateChangeObject() {
		//nothing to go here
	}
	
	function addChange($state, $value) {
		$this->changes[] = array($state, $value);
	}
	
	function commit() {
		$this->commit = true;
	}
	
	function setIrreversible() {
		$this->reversible = false;
	}
	
	function num() {
		return count($this->changes);
	}
	
	function toString() {
		$out = "";
		foreach($this->changes as $key=>$value) {
			if($this->changes[$key][1]) {
				$out.=$this->changes[$key][0].": "."true<br/>";
			}else {
				$out.=$this->changes[$key][0].": "."false<br/>";
			}
		}
		return $out;
	}
}

/***********************************************************************
*                            stateObject
***********************************************************************/

class stateObject{
	
	var $rules = array();
	
	function stateObject(){
		//nothing to do here
	}
	
	function addRule($func) {
		$this->rules[] = $func;
	}
	
	function runRules() {
		if(count($this->rules) > 0)
		{
			foreach($this->rules as $i=>$val) {
				$func = create_function('',$this->rules[$i]);
				if (!$func(''))
					return false;
			}
			return true;
		}
		else
			return false;
	}
}

?>