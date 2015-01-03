<?php

function text_ellipsis($text, $more = '...', $length = 50){
	if(strlen($text) > $length){
		return substr($text, 0, $length) . $more;
	}

	return $text;
}

function is_user_logged_in(){
	return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true;
}