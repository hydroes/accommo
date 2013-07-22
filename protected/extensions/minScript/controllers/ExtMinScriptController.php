<?php

/**
 * minScript Controller.
 *
 * minScript is a Yii Framework Extension which extends clientScript to automatically combine, minify and
 * compress files. The files will be served with optimal client cache headers which will dramatically
 * improve the web application performance and ease the load on web servers.
 *
 * @package ext.minScript.controllers
 * @author total-code
 * @copyright Copyright &copy; 2012 total-code
 * @license BSD 3-clause
 * @link http://bitbucket.org/totalcode/minscript
 * @version 2.0.1
 */
class ExtMinScriptController extends CExtController {

	/**
	 * @var mixed ID of minScript Component as defined in the components property. Automatically gets populated
	 * with the minScript Component instance after it has been validated. Defaults to "clientScript".
	 */
	public $minScriptComponent = 'clientScript';

	/**
	 * Serve files.
	 */
	public function actionServe() {
		require (dirname(dirname(__FILE__)) . '/vendors/minify/min/index.php');
	}

	/**
	 * Ensure that everything is prepared before we execute the serve action.
	 * @param CFilterChain $filterChain Instance of CFilterChain.
	 * @throws CException if minScript Component not defined in the components property.
	 */
	public function filterValidateServe($filterChain) {
		// Check existence of minScript Component inside the components property
		$minScriptComponent = Yii::app() -> getComponent($this -> minScriptComponent);
		if (!($minScriptComponent instanceof ExtMinScript)) {
			throw new CException('ExtMinScript: The minScript Component with ID "' . $this -> minScriptComponent . '" needs to be defined in the components property.');
		}
		$this -> minScriptComponent = $minScriptComponent;
		// Clean output buffer and headers
		@ob_end_clean();
		header('X-Powered-By:');
		header('Pragma:');
		header('Expires:');
		header('Cache-Control:');
		header('Last-Modified:');
		header('Etag:');
		// Process query string
		$get = array();
		if (isset($_GET['g'])) {
			$get['g'] = $_GET['g'];
		}
		if (isset($_GET['debug'])) {
			$get['debug'] = '';
		} elseif (isset($_GET['lm']) && ctype_digit((string)$_GET['lm'])) {
			$get[$_GET['lm']] = '';
		}
		$_GET = $get;
		$_SERVER['QUERY_STRING'] = http_build_query($get, '', '&');
		// Deactive Yii's CWebLogRoute
		if (isset(Yii::app() -> log)) {
			foreach (Yii::app()->log->routes as $route) {
				if ($route instanceof CWebLogRoute) {
					$route -> enabled = false;
				}
			}
		}
		// We're done
		$filterChain -> run();
	}

	/**
	 * Execute filters.
	 * @return array Filters to execute.
	 */
	public function filters() {
		return array('validateServe + serve', );
	}

}
