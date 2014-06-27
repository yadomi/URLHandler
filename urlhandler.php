<?php 

Class URLHandler{

	private $_req = array();
	private $_routes = array();

	/**
	 * Create an URLHandler Object
	 */
	function __construct(){
		$this->_req['request'] 	= str_replace( str_replace('/index.php', '', $_SERVER['PHP_SELF']) , '', $_SERVER['REQUEST_URI']);
		$this->_req['method'] 	= strtolower($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Search for the corresponding route and if so, execute callback()
	 * @return void 
	 */
	public function handle(){
		$found = false;
		if(empty($this->_routes[$this->_req['method']])){
			throw new Exception("The method your trying to use doesn't exist or misspelled", 400);
		}
		foreach ($this->_routes[$this->_req['method']] as $route) {
			if(preg_match('@^' . $route['route'] . '$@', $this->_req['request'], $p_value)){
				array_shift($p_value);
				if(!empty($route['keys'])){
					foreach($p_value as $key => $value){
						$route['parameters'][$route['keys'][$key]] = $value;
					}
					unset($route['keys']);
				}else{
					$route['parameters'] = array();
				}	
				if(!empty($route['parameters']))
					$route['callback']($route['parameters']);			
				else
					$route['callback']();			
				$found = true;
			}
		}
		if(!$found)
			throw new Exception("The method your trying to use doesn't exist or misspelled", 400);
	}

	public function get($regex, Closure $callback){
		$this->setRoute('get', $regex, $callback);
	}

	public function post($regex, Closure $callback){
		$this->setRoute('post', $regex, $callback);
	}

	public function patch($regex, Closure $callback){
		$this->setRoute('patch', $regex, $callback);
	}

	public function put($regex, Closure $callback){
		$this->setRoute('put', $regex, $callback);
	}	

	public function delete($regex, Closure $callback){
		$this->setRoute('delete', $regex, $callback);
	}

	private function setRoute($method, $regex, Closure $callback){
		$regex = trim($regex, '/');
		$url_regex = $regex;
		if (preg_match_all("~\{\s*(.*?)\s*\}~", $regex, $arr)){
  			foreach($arr[0] as $p){
  				$url_regex = str_replace($p, '([a-zA-Z0-9_\+\-%]+)', $url_regex);
  			}
		}
		$route =  '/'.$url_regex.'/?';
		$this->_routes[$method][] = array(
			'route' 	=> $route, 
			'keys' 		=> $arr[1],
			'callback' 	=> $callback
		);
	}

	static function showError(Execption $e){
		http_response_code($e->getCode());	
		return json_encode( array( 'status' => 'error', 'error' => array('code' => $e->getCode(), 'message' => $e->getMessage()) ) );
	}
}

 ?>