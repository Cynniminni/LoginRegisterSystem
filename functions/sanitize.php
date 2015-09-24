<?php

// allows us to output any data and ensure all output is protected against
// XSS attacks
function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}