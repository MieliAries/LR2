<?php
/**
 * Core bootloader
 *
 * @author Serhii Shkrabak
 */

/* RESULT STORAGE */
$RESULT = [
	'state' => 0,
	'data' => [],
	'debug' => []
];

/* ENVIRONMENT SETUP */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'); // Unity entrypoint;

register_shutdown_function('shutdown', 'OK'); // Unity shutdown function

spl_autoload_register('load'); // Class autoloader

set_exception_handler('handler'); // Handle all errors in one function

/* HANDLERS */

/*
 * Class autoloader
 */
function load (String $class):void {
	$class = strtolower(str_replace('\\', '/', $class));
	$file = "$class.php";
	if (file_exists($file))
		include $file;
}

/*
* Debug logger
*/
function printme ( Mixed $var ):void {
	$stack = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[ 0 ];
	$GLOBALS[ 'RESULT' ][ 'debug' ][] = [
	'type' => 'Trace',
	'details' => $var,
	'file' => $stack[ 'file' ],
	'line' => $stack[ 'line' ]
	];
}

/*
 * Error logger
 */
function handler (Throwable $e):void {
	global $RESULT;
	$codes = ['SUCCESS' => 0, 'REQUEST_INCOMPLETE'=> 1, 'REQUEST_INCORRECT' => 2, 'ACCESS_DENIED' => 3,'RESOURCE_LOST' => 4, 'REQUEST_UNKNOWN' => 5, 'INTERNAL_ERROR' => 6, 'TOKEN_EXPIRED' => 7, 'REQUEST_QUERIED' => 8, 'REQUEST_BUSY' => 9, 'ERROR_EXTERNAL' => 10, 'PAYMENT_NEED' => 11, 'ACCESS_LOW' => 12, 'SOFTWARE_EXPIRED' => 13, 'RESOURCE_EXISTS' => 14];
	$message = $e -> getMessage();
	$RESULT['state'] = (isset($codes[$message])) ? $codes[$message] : 6;
	$RESULT[ 'debug' ][] = [
		'type' => get_class($e),
		'details' => $message,
		'file' => $e -> getFile(),
		'line' => $e -> getLine(),
		'trace' => $e -> getTrace()
	];
}

/*
 * Shutdown handler
 */
function shutdown():void {
	global $RESULT;
	$error = error_get_last();
	if ( ! $error ) {
		header("Content-Type: application/json");
		echo json_encode($GLOBALS['RESULT'], JSON_UNESCAPED_UNICODE);
	}
}

$CORE = new Controller\Main;
$data = $CORE->exec();

if ($data !== null)
	$RESULT['data'] = $data;
else { // Error happens
	$RESULT['state'] = 6;
	$RESULT['errors'] = ['INTERNAL_ERROR'];
	unset($RESULT['data']);
}