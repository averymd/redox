<?php

class models {
	
	function models(&$con) {
		$this->outlet =& $con;
		$this->outlet->set('models', $this->outlet->getModels());
	}
	
	function index() {
		
	}

	function add() {
		$model = confirm::exists($_POST['model']);
		$model = confirm::model_name($_POST['model']);

		if (!$model) {
			setFlash('<p class="error">That is not a valid model name. A model name:</p>
			<ul>
				<li>Must be longer than four characters.</li>
				<li>Must be alphanumeric, -, _</li>
			</ul>');
			return;
		} else {
			$model = $_POST['model'];
		}

		$message = '';
		$error = false;

		if (!file_exists(EXPATH.'/app/models/'.$model.'_model.php')) {
			ob_start();
			include(EXPATH.'/sys/tools/_templates/model.php');
			$content = ob_get_contents();
			ob_clean();

			$fh = fopen(EXPATH.'/app/models/'.$model.'_model.php','w+');
			fwrite($fh, $content);
			fclose($fh);
			$message .= '<li class="success">The '.$model.' model was successfully added. (app/models/'.$model.'_model.php)</li>';
		} else {
			$error = true;
			$message .= '<li class="success">The '.$model.' model already exists. (app/models/'.$model.'_model.php)</li>';
		}

		if ($error) {
			setFlash('<p class="error">Some errors occurred. The outcome of your request is outlined below:</p><ul>',$message,'</ul>');
		} else {
			setFlash('<p class="success">Model creation was successful. The outcome of your request is outlined below:</p><ul>',$message,'</ul>');
		}
		
		redirect('generator/models/');
	}

	function delete() {
		$model = confirm::exists($_POST['name']);
		$model = confirm::model_name($_POST['name']);

		if ($model) {
			$model = $_POST['name'];
			if (@unlink(EXPATH.'/app/models/'.$model.'_model.php')) {
				setFlash('<p class="success">The '.$model.' model was successfully deleted.</p>');
			} else {
				setFlash('<p class="error">There was an error deleting the '.$model.' model.</p>');
			}
		} else {
			setFlash('<p class="error">The model you specified does not exist.</p>');
		}
		redirect('generator/models/');
		
	}
	
	function edit() {
		$filename = $this->outlet->uri->segment(2);
		$filepath = EXPATH.'/app/models/'.$filename.'_model.php';
		$this->outlet->_edit($filepath);
	}
}
?>