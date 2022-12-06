<?php

function e($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
}

function sanitize($link, $data){
	return mysqli_real_escape_string($link, $data);
}

?>