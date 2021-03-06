<?php
/**
 * User Controller
 *
 * @author Serhii Shkrabak
 * @global object $CORE
 * @package Controller\Main
 */
namespace Controller;
class Main
{
	use \Library\Shared;

	private $model;

	public function exec():?array {
		$result = null;
		$url = $this->getVar('REQUEST_URI', 'e');
		$path = explode('/', $url);

		if (isset($path[2]) && !strpos($path[1], '.')) { // Disallow directory changing
			$file = ROOT . 'model/config/methods/' . $path[1] . '.php';
			if (file_exists($file)) { //methods exist?
				include $file;
				$file = ROOT . 'model/config/patterns.php';
				if (file_exists($file)) {//pattern exist?
					include $file;
					if (isset($methods[$path[2]])) { //method avaliable?
						$details = $methods[$path[2]];
						$request = [];
						foreach ($details['params'] as $param) {
							$var = $this->getVar($param['name'], $param['source']);
							if ($var){
								if(isset($param['pattern'])) {  //pattern exists?
									if (preg_match($patterns[$param['pattern']]['regex'], $var)){ //input=pattern?
										if(isset($patterns[$param['pattern']]['callback']))
											$var = preg_replace_callback($patterns[$param['pattern']]['regex'], $patterns[$param['pattern']]['callback'], $var);
										$request[$param['name']] = $var;
									}
										else
											throw new \Exception('REQUEST_INCORRECT'); //pattern exists, incorect input
								}else
									$request[$param['name']] = $var;
							}
							else if(!$param['required']){ //parametr not required?
								if(isset($param['default'])) //default value?
									$request[$param['name']] = $param['default']; //default -> not required parametr
									else
										throw new \Exception('INTERNAL_ERROR'); //no default -> error
							}else{
								throw new \Exception('REQUEST_INCOMPLETE'); //parametr required -> error
							}
						}
						if (method_exists($this->model, $path[1] . $path[2])) {
							$method = [$this->model, $path[1] . $path[2]];
							$result = $method($request);
						} else {
							throw new \Exception("REQUEST_UNKNOWN");
						}	
					} else {
						throw new \Exception("REQUEST_UNKNOWN");
					}

				} else { //patterns not avaliable -> error
					throw new \Exception("REQUEST_UNKNOWN");
				}

			} else { //methods not avaliable -> error
				throw new \Exception("REQUEST_UNKNOWN");
			}
		} else { //url not correct -> error
			throw new \Exception("REQUEST_UNKNOWN");
		}

		return $result;
	}

	public function __construct() {
		// CORS configuration
		$origin = $this -> getVar('HTTP_ORIGIN', 'e');
		$front = $this -> getVar('FRONT', 'e');
		foreach ( [$front] as $allowed )
			if ( $origin == "https://$allowed") {
				header( "Access-Control-Allow-Origin: $origin" );
				header( 'Access-Control-Allow-Credentials: true' );
			}
		$this->model = new \Model\Main;
	}
}