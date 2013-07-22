<?php
if (!isset($_GET['g'])) {
	return array();
}
if (($files = $this -> minScriptComponent -> minScriptGetGroup($_GET['g'])) === false) {
	return array();
}
return array($_GET['g'] => $files);
