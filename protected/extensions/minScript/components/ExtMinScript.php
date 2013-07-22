<?php

/**
 * minScript Application Component.
 *
 * minScript is a Yii Framework Extension which extends clientScript to automatically combine, minify and
 * compress files. The files will be served with optimal client cache headers which will dramatically
 * improve the web application performance and ease the load on web servers.
 *
 * @package ext.minScript.components
 * @author total-code
 * @copyright Copyright &copy; 2012 total-code
 * @license BSD 3-clause
 * @link http://bitbucket.org/totalcode/minscript
 * @version 2.0.1
 */
class ExtMinScript extends CClientScript {

	/**
	 * @var string Path to the minScript runtime folder. If left empty, the path will be chosen and created
	 * automatically.
	 */
	public $minScriptRuntimePath;

	/**
	 * @var string ID of minScript Controller as defined in the controllerMap property. Defaults to "min".
	 */
	public $minScriptController = 'min';

	/**
	 * @var boolean Whether the minScript output should be displayed in debug mode. If set to true, files
	 * won't be minified or cached and will be populated with line numbers. Setting this to true will
	 * degrade performance. Defaults to false.
	 */
	public $minScriptDebug = false;

	/**
	 * @var string This property needs to be set to the same URL as the one in the HTML "base" tag. If one
	 * base URL is used for the entire web application, this property can be globally set from the Yii
	 * configuration. The defined URL has to be absolute. Leave empty if no HTML "base" tag is used.
	 */
	public $minScriptBaseUrl = '';

	/**
	 * Initialize Component.
	 * @throws CException if minScript Controller not defined in the controllerMap property.
	 */
	public function init() {
		parent::init();
		// Set/process minScript runtime path
		if (empty($this -> minScriptRuntimePath)) {
			$this -> minScriptRuntimePath = Yii::app() -> runtimePath . '/minScript';
		} else {
			$this -> minScriptRuntimePath = rtrim($this -> minScriptRuntimePath, '/\\');
		}
		// Check existence of minScript Controller inside the controllerMap property
		if (!isset(Yii::app() -> controllerMap[$this -> minScriptController])) {
			throw new CException('ExtMinScript: The minScript Controller with ID "' . $this -> minScriptController . '" needs to be defined in the controllerMap property.');
		}
	}

	/**
	 * Initialize a folder for minScript.
	 * @param string $path Path to folder.
	 * @throws CException if folder couldn't be created or isn't writable.
	 */
	public function minScriptInitDir($path) {
		// Create folder if necessary
		if (@is_dir($path) === false) {
			@mkdir($path, 0777, true);
		}
		// Check folder
		if (@is_writable($path) !== true) {
			throw new CException('ExtMinScript: ' . $path . ' is not writable or couldn\'t be created. Please check file and folder permissions.');
		}
	}

	/**
	 * Get the file system path from a URL.
	 * @param string $url The URL for which to get the path.
	 * @return string The absolute path with no trailing slash.
	 */
	public function minScriptGetPath($url) {
		// Get document root
		$docRoot = rtrim(substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['SCRIPT_NAME'])), '/\\');
		// Check if absolute path was passed as parameter
		if (strpos($url, str_replace('/', DIRECTORY_SEPARATOR, $docRoot)) === 0 || strpos($url, $docRoot) === 0) {
			Yii::log('An absolute path might have been passed to minScriptGetPath', CLogger::LEVEL_WARNING, 'ext.minScript.components.ExtMinScript');
		}
		// Process specified URL
		if (strpos($url, Yii::app() -> assetManager -> baseUrl) === 0) {
			// The URL points to an asset
			$assetBasePath = rtrim(Yii::app() -> assetManager -> basePath, '/\\');
			$path = $assetBasePath . substr($url, strlen(Yii::app() -> assetManager -> baseUrl));
		} elseif (strpos($url, '/') === 0) {
			// The URL is relative to the document root
			$path = $docRoot . $url;
		} elseif (preg_match('/^[a-z0-9\.+-]+:\/\//i', $url) > 0) {
			// The URL is absolute
			$path = $docRoot . @parse_url($url, PHP_URL_PATH);
		} else {
			// The URL is relative to the current request
			$requestPathRaw = (($requestPathRaw = @parse_url(Yii::app() -> request -> hostInfo . Yii::app() -> request -> url, PHP_URL_PATH)) && substr($requestPathRaw, -1) == '/') ? $requestPathRaw .= 'dummy' : $requestPathRaw;
			$requestPath = rtrim(dirname($requestPathRaw), '/\\');
			if (!empty($this -> minScriptBaseUrl)) {
				if (preg_match('/^[a-z0-9\.+-]+:\/\//i', $this -> minScriptBaseUrl) < 1) {
					Yii::log('minScriptBaseUrl is not an absolute URL', CLogger::LEVEL_ERROR, 'ext.minScript.components.ExtMinScript');
				}
				$basePathRaw = (($basePathRaw = @parse_url($this -> minScriptBaseUrl, PHP_URL_PATH)) && substr($basePathRaw, -1) == '/') ? $basePathRaw .= 'dummy' : $basePathRaw;
				$basePath = rtrim(dirname($basePathRaw), '/\\');
			}
			$path = (isset($basePath)) ? $docRoot . $basePath . '/' . $url : $docRoot . $requestPath . '/' . $url;
		}
		return rtrim($path, '/\\');
	}

	/**
	 * The minScript Processor starts the group creation process and processes the clientScript properties.
	 * @param string $type Type of files to process (css/scripts).
	 * @param integer $position Position of scripts which should get processed. Not needed for CSS files.
	 */
	protected function _minScriptProcessor($type, $position = '') {
		if ($type == 'scripts') {
			// Process script files
			if (isset($this -> scriptFiles[$position])) {
				$files = array();
				foreach ($this->scriptFiles[$position] as $scriptUrl) {
					$files[] = $this -> minScriptGetPath($scriptUrl);
				}
				unset($this -> scriptFiles[$position]);
				$groupUrl = $this -> minScriptCreateGroup($files);
				$this -> scriptFiles[$position][$groupUrl] = $groupUrl;
			}
		} elseif ($type == 'css') {
			// Process CSS files
			$cssFiles = array();
			foreach ($this->cssFiles as $cssUrl => $cssMedia) {
				$cssFiles[$cssMedia][] = $this -> minScriptGetPath($cssUrl);
				unset($this -> cssFiles[$cssUrl]);
			}
			foreach ($cssFiles as $cssMedia => $files) {
				$groupUrl = $this -> minScriptCreateGroup($files);
				$this -> cssFiles[$groupUrl] = $cssMedia;
			}
		}
	}

	/**
	 * Create a minScript group from the supplied files and return the URL for the group. If a group with
	 * the same files & order exists, the group creation will be skipped and the URL for the existing group
	 * returned.
	 * @param array $files An array of files. The files can be defined with absolute or relative paths.
	 * @return string URL for the group.
	 */
	public function minScriptCreateGroup($files) {
		// Path to minScript groups folder
		$groupsPath = $this -> minScriptRuntimePath . '/groups';
		// Generate ID
		$files = (array)$files;
		$filesSerialized = serialize($files);
		$groupId = md5($filesSerialized);
		// Garbage collection
		if (($groupsDirModTime = @filemtime($groupsPath)) !== false && (time() - $groupsDirModTime) > 864000) {
			$deleteFiles = glob($groupsPath . '/*', GLOB_NOSORT);
			if (!empty($deleteFiles)) {
				foreach ($deleteFiles as $deleteFile) {
					@unlink($deleteFile);
				}
			}
		}
		// Group creation necessary?
		if (@is_file($groupsPath . '/' . $groupId) === false) {
			// Initialize groups folder
			$this -> minScriptInitDir($groupsPath);
			// Create group
			@file_put_contents($groupsPath . '/' . $groupId, $filesSerialized, LOCK_EX);
		}
		// Get file modification timestamps
		$fileModTimes = array();
		foreach ($files as $file) {
			if (($fileModTime = @filemtime($file)) !== false) {
				$fileModTimes[] = $fileModTime;
			} else {
				Yii::log('Can\'t access ' . $file, CLogger::LEVEL_ERROR, 'ext.minScript.components.ExtMinScript');
			}
		}
		// Generate URL for group
		$params = array();
		$params['g'] = $groupId;
		if ($this -> minScriptDebug === true) {
			$params['debug'] = 1;
		} elseif (!empty($fileModTimes)) {
			$params['lm'] = max($fileModTimes);
		}
		return Yii::app() -> createUrl($this -> minScriptController . '/serve', $params);
	}

	/**
	 * Get files from a minScript group.
	 * @param string $id ID of the group.
	 * @return mixed Array of files from the group or false if group doesn't exist.
	 */
	public function minScriptGetGroup($groupId) {
		// Path to minScript groups folder
		$groupsPath = $this -> minScriptRuntimePath . '/groups';
		// Get group
		if (($filesSerialized = @file_get_contents($groupsPath . '/' . $groupId)) === false) {
			return false;
		} else {
			return unserialize($filesSerialized);
		}
	}

	/**
	 * Inserts the scripts at the beginning of the body section (overrides parent method).
	 * @param string $output the output to be inserted with scripts.
	 */
	public function renderBodyBegin(&$output) {
		// Run minScript Processor
		$this -> _minScriptProcessor('scripts', self::POS_BEGIN);
		// Run parent method
		parent::renderBodyBegin($output);
	}

	/**
	 * Inserts the scripts at the end of the body section (overrides parent method).
	 * @param string $output the output to be inserted with scripts.
	 */
	public function renderBodyEnd(&$output) {
		// Run minScript Processor
		$this -> _minScriptProcessor('scripts', self::POS_END);
		// Run parent method
		parent::renderBodyEnd($output);
	}

	/**
	 * Inserts the scripts in the head section (overrides parent method).
	 * @param string $output the output to be inserted with scripts.
	 */
	public function renderHead(&$output) {
		// Run minScript Processor
		$this -> _minScriptProcessor('scripts', self::POS_HEAD);
		$this -> _minScriptProcessor('css');
		// Run parent method
		parent::renderHead($output);
	}

}
