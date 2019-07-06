<?php
/*
Quick functions file for ajax scripts, so not to tie into CI
*/

function readTemplate($filename)
{
	global $_TEMPLATE_PATH;
	$handle   = fopen($_TEMPLATE_PATH."/".$filename.".php", "r");
	$contents = fread($handle, filesize($_TEMPLATE_PATH."/".$filename.".php"));
	fclose($handle);
	return $contents;

}// end

function debug($name,$value)
{
	echo "*$name*/*$value*";
}// end


?>