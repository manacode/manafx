<?php
include("../config/constants.php");
if (file_exists(CORE_PATH . 'init.php')) {
	require CORE_PATH . 'init.php';
} else {
	echo "Error Initialization!";
}