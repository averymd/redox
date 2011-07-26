<?php

/*	CLASS Benchmark
 *	Used to gather time information for the profiler.
 *
 *	@author Chris
 */

class Benchmark {
	var $marks = array(); // Associative array of start and end mark times
	var $queries = array(); // Array of all queries performed during the controller execution
	var $queryNum = 0;
	var $errors = array();

	/*	CONSTRUCTOR
	 *	Does nothing.
	 *
	 *	@author Chris
	 */
	function Benchmark() {
		// Does nothing.
	}

	/*	logQuery
	 *	This function logs any query performed via $this->db->query().
	 *	Records both the start-time of the query and the actual sql executed.
	 *
	 *	@param $sql = the text of the query
	 *	@author Chris
	 */
	function logQuery($sql) {
		$this->queries[$this->queryNum][0] = $sql;
		$this->queries[$this->queryNum][1] = $this->microtime_float();
		return $this->queryNum;
	}

	/*	failedQuery
	 *	This function notes a failed query and stores the error message.
	 *
	 *	@author Chris
	 */
	function failedQuery($error) {
		$this->queries[$this->queryNum][2] = $error;
	}

	/*	endQuery
	 *	This function is called after log query to figure out the end time of the query
	 *	and to increment the number of queries recorded
	 *
	 *	@author Chris
	 */
	function endQuery() {
		if (!isset($this->queries[$this->queryNum][2])) {
			$this->queries[$this->queryNum][1] = $this->microtime_float()-$this->queries[$this->queryNum][1];
		} else {
			$this->queries[$this->queryNum][1] = $this->queries[$this->queryNum][2];
		}
		$this->queryNum++;
	}

	/*	start
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	function start($name) {
		$this->marks[$name][0] = $this->microtime_float();
	}

	/*	end
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	function end($name) {
		$this->marks[$name][1] = $this->microtime_float();
	}

	/*	time
	 *	This function returns the total time between the start and end of a mark with index $name
	 *
	 *	@param $name = name of the mark
	 *	@return float
	 *	@author Chris
	 */
	function time($name) {
		return $this->marks[$name][1] - $this->marks[$name][0];
	}
	
	function addError($level, $file, $line, $message) {
		$this->errors[] = array($level, $file, $line, $message);
	}

	/*	microtime_float
	 *	This utility function returns the float version of microtime()
	 *
	 *	@return float of current microtime
	 */
	function microtime_float() {
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}

}

?>