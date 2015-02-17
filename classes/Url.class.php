<?php
class URL {
   
  function set_as_crawled($url_id, $return_code = 200) {
    global $con;
    $sql = "UPDATE url SET crawled = 1, return_code = $return_code WHERE url_id=" . $url_id;
    $result = mysqli_query($con,$sql);
    if (!$result) {
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
  }


}