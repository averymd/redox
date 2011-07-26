<?php echo '<?php'; ?>

class <?php echo $name ?>_controller extends Controller
{
	function <?php echo $name ?>_controller() {
		
		parent::Controller();
		
		//if(!$this->access->loggedIn() && FUNC != 'login') { redirect('<?php echo $name ?>/login'); }

		//load models
		$this->model('<?php echo $name ?>');
	}

	/********************************************************
	*		<?php echo $name ?> functions
	*********************************************************/
	
	function index() {
		$this->set('rows', $this-><?php echo $name ?>->getAll());	
	}
	
	function add<?php echo ucwords($name); ?>() {
		if(!$this->access->to('add <?php echo $name ?>')) { redirect('<?php echo $name ?>/index'); }

		if($this->validator->validateForm('<?php echo $name ?>')) {
			$this-><?php echo $name ?>->add();
			setFlash('The <?php echo $name ?> was added');
			redirect('<?php echo $name ?>/index');
		}
	}

	function edit<?php echo ucwords($name) ?>() {
		if(!$this->access->to('edit <?php echo $name ?>')) { redirect('<?php echo $name ?>/index'); }
	
		//grab <?php echo $name ?> ID from the uri
		$<?php echo $name ?>ID = $this->uri->segment(1);
		$this->set('ID', $<?php echo $name ?>ID);
		
		//validate and update
		if($this->validator->validateForm('<?php echo $name ?>')) {
			$this-><?php echo $name ?>->update($<?php echo $name ?>ID);
			setFlash('The <?php echo $name ?> has been edited');
			redirect('<?php echo $name ?>/index');
		}

		if(!$this->validator->posted) {
			$cur<?php echo ucwords($name); ?> = $this-><?php echo $name ?>->get($<?php echo $name ?>ID);
			$this->validator->preloadFormVals($cur<?php echo ucwords($name); ?>);
		}
	}
	
	function delete<?php echo ucwords($name); ?>() {
		$<?php echo $name ?>ID = $this->uri->segment(1);

		if(!$this->access->to('delete <?php echo $name ?>')) { redirect("<?php echo $name ?>/"); }

		$this-><?php echo $name ?>->delete($<?php echo $name ?>ID);
		setFlash('The <?php echo $name; ?> has been deleted');
		redirect("<?php echo $name ?>/");
	}
}

<?php echo '?>'; ?>