<?php

$options = getopt("r:l:");
// r = remote = example: https://aset.ubaya.ac.id/ubayaadum/images (without trailing slash, without asterisk)
// l = local = example: /tmp/images (without trailing slash, without asterisk)
// example usage: php check_existence_of_file_in_remote_server.php -l/tmp/images -rhttp://mysite.com/images

$optLocalDirectory = $options["l"];
$optRemoteUrl = $options["r"];

$found = array(); $notfound = array();

function iterate($optLocalDirectory, $optRemoteUrl) {
	global $found, $notfound;
	$files = glob($optLocalDirectory."/*");
	foreach ($files as $key => $value) {
		$filename = str_replace($optLocalDirectory."/", "", $value);

		if(is_dir($value)) {
			iterate($value, $optRemoteUrl);
		} else {
			$remoteUrl = $optRemoteUrl."/".$filename;
			$content = file_get_contents($remoteUrl);
			if($content === false) {
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

echo "\r\n\r\nfound: \r\n";
print_r($found);
echo "\r\n\r\nnot found: \r\n";
print_r($notfound);

echo "found: ".count($found).", not found: ".count($notfound); 
echo "\r\n\r\n";
echo json_encode(array("found"=>$found, "notfound"=>$notfound));
echo "\r\n";
