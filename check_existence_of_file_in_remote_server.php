<?php

$timestart = microtime(true);

$options = getopt("r:l:");
// r = remote = example: https://aset.ubaya.ac.id/ubayaadum/images (without trailing slash, without asterisk)
// l = local = example: /tmp/images (without trailing slash, without asterisk)
// example usage: php check_existence_of_file_in_remote_server.php -l/tmp/images -rhttp://mysite.com/images

$optLocalDirectory = $options["l"];
$optRemoteUrl = $options["r"];

$found = array(); $notfound = array();

function file_get_contents_headonly_followredirect($url)
{
	// from: https://stackoverflow.com/questions/981954/how-can-one-check-to-see-if-a-remote-file-exists-using-php
	//$url = 'http://example.com/';
	$code = false;

	$options['http'] = array(
	    'method' => "HEAD",
	    'ignore_errors' => 1
	);

	$body = file_get_contents($url, null, stream_context_create($options));

	foreach($http_response_header as $header)
	    sscanf($header, 'HTTP/%*d.%*d %d', $code);

	//echo "Status code: $code";
	return $code;
}

function iterate($optLocalDirectory, $optRemoteUrl) {
	global $found, $notfound;
	$files = glob($optLocalDirectory."/*");
	foreach ($files as $key => $value) {
		$filename = str_replace($optLocalDirectory."/", "", $value);

		if(is_dir($value)) {
			iterate($value, $optRemoteUrl);
		} else {
			$remoteUrl = $optRemoteUrl."/".$filename;
			//$content = file_get_contents($remoteUrl);
			$statuscode = file_get_contents_headonly_followredirect($remoteUrl);
			//if($content === false) {
			if($statuscode === false || in_array(substr($statuscode, 0, 1), array("4","5")) ) { // if statuscode starts with 4xx (user error) or 5xx (server error)
				echo "not found: ".$filename;
				$notfound[] = array("filename"=>$filename, "local"=>$value, "remote"=>$remoteUrl);
			} else {
				echo "found: ".$filename;
				$found[] = array("filename"=>$filename, "local"=>$value, "remote"=>$remoteUrl);
			}
		}
	}
}
iterate($optLocalDirectory, $optRemoteUrl);

$timeend = microtime(true);

echo "\r\n\r\nfound: \r\n";
print_r($found);
echo "\r\n\r\nnot found: \r\n";
print_r($notfound);

echo "found: ".count($found).", not found: ".count($notfound); 
echo "\r\n\r\n";
echo json_encode(array("found"=>$found, "notfound"=>$notfound));
echo "\r\n";

echo "run in: ".($timeend-$timestart)." second(s)";
