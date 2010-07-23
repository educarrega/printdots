<?php
// Set your return content type
header('Content-type: application/xml');

// Website url to open
if(!$daurl) $daurl = 'http://printdots.com.br/?feed=rss2&cat=4';

// Get that website's content
$handle = fopen($daurl, "r");

// If there is something, read and return
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        echo $buffer;
    }
    fclose($handle);
}
?>