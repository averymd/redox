<?php

class home_controller extends Controller {

  function home_controller() {
    parent::Controller();
    $this->useLayout('main');
  }
  
  function index() {
    $this->set('title', 'Home');
    
    $this->model('category');
    $this->set('categories', $this->category->getAll());
  }

}

?>
