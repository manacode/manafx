<?php

class ManafxModel extends \Phalcon\Mvc\Model
{
	var $query_limit = 200;
	var $page = 1;
	var $offset = 0;
	var $rowcount = 0;
	var $ConnectionService = "db";

	public function initialize() {
		if(method_exists($this, "before_initialize")) {
			$this->before_initialize();
		}
		$di = $this->getDI();
		
		if(method_exists($this, "init")) {
			$this->init();
		}

		if(method_exists($this, "getConnectionService")) {
			$this->ConnectionService = $this->getConnectionService();
			$this->setConnectionService($this->ConnectionService);
			
			// Profiler fire event pass connection service
			if ($di->get("config")->system->profiler_mode=="on") {
				$this->getDI()['profiler']->setEventDB($this->ConnectionService, $this->getClassName());
			}
		}
		
		if(method_exists($this, "after_initialize")) {
			$this->after_initialize();
		}
		
	}
	
	public function getSource()
	{
    return $this->getDI()->getConfig()->database->tableprefix . $this->getClassName();
	}
	
  function getClassName()
	{
	  $classname = strtolower(get_class($this));
	
	  if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
	      $classname = $matches[1];
	  }
		
	  return $classname;
	}
	
	public function pfind($p)
	{
		$di = $this->getDI();
		// start pagination
		if (isset($this->getDI()->getConfig()->database->query_limit)) {
			$this->query_limit = $this->getDI()->getConfig()->database->query_limit;
		}
		if ($di->get('request')->getQuery('page')!==NULL) {
			$this->page = $di->get('request')->getQuery('page');
		}
		$this->offset = ($this->page - 1) * $this->query_limit;
		// end pagination

		$p2 = $p;
		if (!is_array($p)) {
			$p2 = array("condition" => $p);
		}
		
		$this->rowcount = $this::count($p2);
		
		$p2["limit"] = $this->query_limit;
		$p2["offset"] = $this->offset;
		
		$result = $this::find($p2);
		return $result;
	}

  function getPager($config=array()) {
    $config['per_page'] = $this->query_limit;
    $config['total_rows'] = $this->rowcount;
    $config['cur_page'] = $this->page;
    
    $config['full_tag_open'] = '<ul class="pagination pull-right">';
    $config['full_tag_close'] = '</ul>';
    
    $config['first_link'] = 'First';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    
    $config['last_link'] = 'Last';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    
    $config['prev_link'] = '&laquo;';
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = '&raquo;';
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';

    $pages = new Paginator($config);
    return $pages->create_links();
    
  }
}