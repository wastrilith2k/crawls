<?php

include('includes/db.inc.php');

// Make sure to set the content type to utf8
header('Content-Type: text/html; charset=utf-8');

// Show results
$sql = "SELECT * FROM url WHERE crawled = 1";
$result = mysqli_query($con,$sql);
if (!$result) {
  die('Error: ' . mysqli_error($con) . ' in ' . $sql);
}
while($row = mysqli_fetch_array($result)) {
  print '<fieldset><legend><a href="' . stripslashes($row['url']) . '">' . $row['title'] . '</a></legend>';
	print stripslashes($row['url']) . "<br />\n";

	if ($row['has_image'] == 0) {
	  $src = "http://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg";
	} else {
	  $src = 'img.php?url_id=' . $row['url_id'];
	}	
	print '<img src="' . $src . '" />' . "\n";
  print nl2br(stripslashes($row['content']));
  print "</fieldset>\n";
}
