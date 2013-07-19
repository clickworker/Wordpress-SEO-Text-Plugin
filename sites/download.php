<?php

if($_GET['task_id'] && preg_match('/^[\d]+$/', $_GET['task_id'])){
	//File download form Server
	$response = cw_command($url, "GET");
}

?>