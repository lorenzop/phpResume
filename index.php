<?php

define('CRLF', "\r\n");
$default_spacer = 15;
$tokens = array();



// view resume by token
$token = @$_GET['token'];
if(!empty($token)) {
	load_tokens();
	$name = @$tokens[$token];
	// token not found
	if(empty($name)) {
		error('Sorry, the token you entered <i>'.$token.'</i> was not recognized.');
	} else
	if(load_resume($name)) {
		exit();
	}
}



// default page
html_open();
page_open();

page_home();

page_close();
html_close();



function load_tokens() {
	global $tokens;
	$data = @file_get_contents('tokens.txt');
	$data = str_replace(array("\r", "\t"), array("\n", ' '), $data);
	$lines = explode("\n", $data);
	foreach($lines as $line) {
		$line = trim($line);
		if(empty($line)) continue;
		list($key, $val) = explode(' ', $line, 2);
		$key = trim($key);
		$val = str_replace(' ', '_', trim($val));
		$tokens[$key] = $val;
	}
}



function load_resume($name) {
	global $token;
	$file = preg_replace('/[^0-9a-zA-Z_]/', '', $name).'.php';
	if(!file_exists($file)) {
		error('Sorry, the token you entered <i>'.$token.'</i> failed to find file <i>'.$file.'</i>.');
		return false;
	}
	include($file);
	return true;
}



function error($msg) {
	global $errors;
	$errors[] = $msg;
}



// html head
function html_open() {
echo
'<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
</head>
<body>
<div class="container" role="main">
'.CRLF.CRLF;
}
// html foot
function html_close() {
echo '
</div>
</body>
</html>
'.CRLF.CRLF;
}



// page header
function page_open() {
	global $errors;
	if(!empty($errors)) {
		foreach($errors as $err)
			echo '<div style="margin-top: 10px;" class="alert alert-danger" role="alert">'.$err.'</div>'.CRLF;
	}
echo '
<h1 style="margin-bottom: 50px;" class="text-center"><span style="font-size: 65%;">PHP</span>Resume</h1>
'.CRLF.CRLF;
}
function page_close() {
echo '
'.CRLF.CRLF;
}



function page_home() {
echo '
<div style="margin-left: auto; margin-right: auto; max-width: 500px; margin-bottom: 50px;" class="panel panel-primary">
	<div class="panel-heading"></div>
	<div class="panel-body">
		<form role="form">
		<div class="input-group">
			<input type="text" name="token" class="form-control" placeholder="token" />
			<span class="input-group-btn">
				<button type="button" class="btn btn-default">View</button>
			</span>
		</div>
		</form>
	</div>
</div>
'.CRLF.CRLF;
echo '
<div style="margin-left: auto; margin-right: auto; max-width: 500px; margin-bottom: 50px;" class="panel panel-default">
	<div class="panel-heading">Admin</div>
	<div class="panel-body">
		<form role="form">
		<div class="input-group">
			<input type="text" name="password" class="form-control" placeholder="password" />
			<span class="input-group-btn">
				<button type="button" name="viewtokens" class="btn btn-default">View Tokens</button>
			</span>
		</div>
		</form>
	</div>
</div>
'.CRLF.CRLF;
}



function resume_head($name='', $address1='', $address2='', $phone='', $email='') {
echo
'<!DOCTYPE html>
<html>
<head>
<title>'.$name.'</title>
<style>
html, body {
	margin-left:   0;
	margin-right:  0;
	margin-top:    0;
	margin-bottom: 0;
}
table {
	border-width: 0;
	border-spacing: 10px;
	border-collapse: separate;
}
td {
	vertical-align: top;
}
@media screen {
	.printonly {
		display: none;
	}
}
</style>
<script language="javascript"><!--
//--></script>
</head>
<body>
<table width="100%">
'.CRLF.CRLF;
	echo '<tr><td colspan="2" align="center">'.CRLF;
	if(!empty($name))
		echo '<b>'.$name.'</b><br />'.CRLF;
	if(!empty($address1))
		echo $address1.'<br />'.CRLF;
	if(!empty($address2))
		echo $address2.'<br />'.CRLF;
	if(!empty($phone))
		echo $phone.'<br />'.CRLF;
	if(!empty($email))
		echo $email.'<br />'.CRLF;
	echo '</td></tr>'.CRLF;
	spacer();
}
// second page header
function resume_head_second($name='', $page=2) {
	echo '<tr class="printonly"><td colspan="2" align="center" style="page-break-inside: always;">'.CRLF.
		(empty($name) ? '' : $name.'<br />'.CRLF).
		'<span style="font-size: 90%;">(Page '.((int) $page).')</span>'.CRLF.
		'</td></tr>'.CRLF;
	spacer();
}



function resume_definition($title, $msg='') {
//	$title = strtoupper($title);
	echo '<tr>'.CRLF.
		'<td><b>'.$title.'</b></td>'.CRLF.
		'<td>'.$msg.'</td>'.CRLF.
		'</tr>'.CRLF;
	spacer();
}



function resume_entry($date, $title, $msg) {
	// date
	if(strpos($date, ' to ') !== FALSE) {
		list($date1, $date2) = explode(' to ', $date, 2);
		$date = $date1.'<br />'.CRLF.
			'to<br />'.CRLF.
			$date2.CRLF;
	}
	// title
	if(empty($title))
		;
	else
	if(strpos($title, '(') !== FALSE && strpos($title, ')') !== FALSE) {
		list($title1, $title2) = explode('(', $title, 2);
		$title = '<b>'.$title1.'</b>'.'('.$title2;
	} else {
		$title = '<b>'.$title.'</b>';
	}
	// messages
	$msg = '';
	if(func_num_args() > 2) {
		for($i=0; $i<func_num_args()-2; $i++) {
			$line = func_get_arg($i+2);
			if(empty($line)) continue;
			$msg .= $line.'<br />';
		}
	}
	// build html
	echo '<tr>'.CRLF.
		'<td align="center">'.CRLF.
			'<span style="font-size: 90%">'.$date.'</span>'.CRLF.
		'</td>'.CRLF.
		'<td align="left">'.CRLF.
			(empty($title) ? '' : '<span style="font-size: small;">'.$title.'</span><br />'.CRLF).
			(empty($msg)   ? '' : '<span style="font-size: smaller;">'.$msg.'</span>'.CRLF).
		'</td>'.CRLF.
		'</tr>'.CRLF.
	spacer();
}



function spacer($height=NULL) {
	global $default_spacer;
	if($height === NULL)
		$height = $default_spacer;
	if($height !== NULL && $height > 0)
		echo '<tr><td height="'.((int) $height).'"></tr>'.CRLF;
}



?>
