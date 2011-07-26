<?php
class controllers {
	
	var $outlet;

	function controllers(&$con) {
		$this->outlet = $con;
		$this->outlet->set('controllers', $this->outlet->getControllers());
	}
	
	function index() {
		
	}

	function add() {
		$cont = confirm::exists($_POST['controller']);
		$cont = confirm::controller_name($_POST['controller']);

		if (!$cont) {
			setFlash('<p class="error">That is not a valid controller name. A controller name:</p>
			<ul>
				<li>Must be longer than four characters.</li>
				<li>Must be alphanumeric, -, _</li>
				<li>May not be "guide" or "generator".</li>
			</ul>');
			return;
		} else {
			$cont = $_POST['controller'];
		}

		$message = '';
		$error = false;

		// Controller
		if (!file_exists(EXPATH.'/app/controllers/'.$cont.'_controller.php')) {
			ob_start();
			include(EXPATH.'/sys/tools/_templates/controller.php');
			$content = ob_get_contents();
			ob_clean();
			$fh = fopen(EXPATH.'/app/controllers/'.$cont.'_controller.php','w+');
			fwrite($fh, $content);
			fclose($fh);

			$message .= '<li class="success">The '.$cont.' controller was successfully added. (app/controllers/'.$cont.'_controller.php)</li>';
		} else {
			$error = true;
			$message .= '<li class="error">The '.$cont.' controller already exists. (app/controllers/'.$cont.'_controller.php)</li>';
		}

		// Layout
		if (!file_exists(EXPATH.'/app/views/_layouts/'.$cont.'.php')) {
			ob_start();
			include(EXPATH.'/sys/tools/_templates/layout.php');
			$content = ob_get_contents();
			ob_clean();

			$fh = fopen(EXPATH.'/app/views/_layouts/'.$cont.'.php','w+');
			fwrite($fh, $content);
			fclose($fh);
			$message .= '<li class="success">The layout for the '.$cont.' controller was successfully added. (app/views/_layouts/'.$cont.'.php)</li>';
		} else {
			$error = true;
			$message .= '<li class="error">The layout for the '.$cont.' controller already exists. (app/views/_layouts/'.$cont.'.php)</</li>';
		}

		// View folder
		if (!file_exists(EXPATH.'/app/views/'.$cont)) {
			mkdir(EXPATH.'/app/views/'.$cont);
			$message .= '<li class="success">The views folder for the '.$cont.' controller was successfully created. (app/views/'.$cont.')</li>';
		} else {
			$error = true;
			$message .= '<li class="error">The views folder for the '.$cont.' controller already exists. (app/views/'.$cont.')</li>';
		}

		// View index
		if (!file_exists(EXPATH.'/app/views/'.$cont.'/index.php')) {
			$fh = fopen(EXPATH.'/app/views/'.$cont.'/index.php','w+');
			fclose($fh);
			$message .= '<li class="success">The index view for the '.$cont.' controller was successfully added. (app/views/'.$cont.'/index.php)</li>';
		} else {
			$error = true;
			$message .= '<li class="error">The index view for the '.$cont.' controller already exists. (app/views/'.$cont.'/index.php)</li>';
		}

		if ($error) {
			setFlash('<p class="error">Some errors occurred. The outcome of your request is outlined below:</p><ul>'.$message.'</ul>');
		} else {
			setFlash('<p class="success">Controller creation was successful. The outcome of your request is outlined below:</p><ul>'.$message.'</ul>');
		}
		
		redirect('generator/controllers/');
	}

	function delete() {
		$controller = confirm::exists($_POST['name']);
		$controller = confirm::controller_name($_POST['name']);

		if ($controller) {
			$controller = $_POST['name'];
			$message = '';
			$error = false;

			if (@unlink(EXPATH.'/app/controllers/'.$controller.'_controller.php')) {
				$message .= '<li class="success">The '.$controller.' controller file was successfuly deleted.</li>';
			} else {
				$error = true;
				$message .= '<li class="error">There was an error deleting the '.$controller.' controller.</li>';
			}

			if (@unlink(EXPATH.'/app/views/_layouts/'.$controller.'.php')) {
				$message .= '<li class="success">The '.$controller.' controller layout file was successfuly deleted.</li>';
			} else {
				$error = true;
				$message .= '<li class="error">There was an error deleting the '.$controller.' layout file.</li>';
			}

			if (deltree(EXPATH.'/app/views/'.$controller)) {
				$message .= '<li class="success">The '.$controller.' controller views folder was successfuly deleted.</li>';
			} else {
				$error = true;
				$message .= '<li class="error">There was an error deleting the '.$controller.' controller views folder.</li>';
			}

			if ($error) {
				setFlash('<p class="error">Some errors occurred. The outcome of your request is outlined below:</p><ul>'.$message.'</ul>');
			} else {
				setFlash('<p class="success">Controller deletion was successful. The outcome of your request is outlined below:</p><ul>'.$message.'</ul>');
			}
		} else {
			setFlash('<p class="error">The controller you specified ('.treat::xss($controller).') does not exist.</p>');
		}
		
		redirect('generator/controllers/');
		
	}
	
	function edit() {
		$filename = $this->outlet->uri->segment(2);
		$filepath = EXPATH.'/app/controllers/'.$filename.'_controller.php';
		$this->outlet->_edit($filepath);
	}
}
?>