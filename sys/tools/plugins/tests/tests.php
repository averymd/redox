<?php

class tests {

	function tests(&$con) {
		$this->outlet =& $con;
		$this->outlet->set('tests', $this->outlet->getTests());
	}
	
	function index() {
		
	}

	function addtest() {
		$test = confirm::exists($_POST['test']);
		$test = confirm::test_name($_POST['test']);

		if (!$test) {
			setFlash('<p class="error">That is not a valid test name. A test name:</p>
			<ul>
				<li>Must be alphanumeric, -, _</li>
			</ul>');
			return;
		} else {
			 $test = $_POST['test'];
		}

		$message = '';
		$error = false;

		if (!file_exists(EXPATH.'/app/tests/'.$test.'_test.php')) {
			ob_start();
			include(EXPATH.'/sys/tools/_templates/test.php');
			$content = ob_get_contents();
			ob_clean();

			$fh = fopen(EXPATH.'/app/tests/'.$test.'_test.php','w+');
			fwrite($fh, $content);
			fclose($fh);
			$message .= '<li class="success">The '.$test.' test was successfully added. (app/tests/'.$test.'_test.php)</li>';
		} else {
			$error = true;
			$message .= '<li class="success">The '.$test.' model already exists. (app/tests/'.$test.'_test.php)</li>';
		}

		if ($error) {
			setFlash('<p class="error">Some errors occurred. The outcome of your request is outlined below:</p><ul>',$message,'</ul>');
		} else {
			setFlash('<p class="success">Test creation was successful. The outcome of your request is outlined below:</p><ul>',$message,'</ul>');
		}
		redirect('generator/tests');
		
	}

	function deletetest() {
		$test = confirm::exists($_POST['name']);
		$test = confirm::test_name($_POST['name']);

		if ($test) {
			$test = $_POST['name'];
			if (@unlink(EXPATH.'/app/tests/'.$test.'_test.php')) {
				setFlash('<p class="success">The '.$test.' test was successfully deleted.</p>');
			} else {
				setFlash('<p class="error">There was an error deleting the '.$test.' test.</p>');
			}
		} else {
			setFlash('<p class="error">The test you specified does not exist.</p>');
		}
		redirect('generator/tests');
		
	}

	function test() {
		if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$test = confirm::exists($_POST['name']);
			$test = confirm::test_name($_POST['name']);

			if ($test) {
				$test = $_POST['name'];
				include(EXPATH.'/sys/libraries/unit.php');
				$run = new TestRunner();
				$run->addUnit($test.'_test');
				$run->runUnits();
				setFlash('<h3>Test '.$test.' was successfully run.</h3><pre style="margin-top: 1em;">'.$run->results().'</pre>');
			} else {
				setFlash('<p class="error">No such test exists.</p>');
			}
		}

		redirect('generator/tests');
	}
	
	function edit() {
		$filename = $this->outlet->uri->segment(2);
		$filepath = EXPATH.'/app/tests/'.$filename.'_test.php';
		$this->outlet->_edit($filepath);
	}
	
}
?>