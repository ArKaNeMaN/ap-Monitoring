<?php
	require '../lib/engine_class.php';
	$eng = new engine(true);
	
	switch($_GET['action']){
		case 'test': {
			$eng->ajaxReturn($eng->modules['monitoring']->getServers());
		}
		case 'getServers': $eng->ajaxReturn($eng->modules['monitoring']->getServers());
	}
?>