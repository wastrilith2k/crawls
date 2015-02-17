<?php

include_once('lib/simple_html_dom.php');
include_once('includes/crawl_config.inc.php');
include_once('includes/db.inc.php');
include_once('includes/logging.inc.php');

watchdog("Using character set: %s", mysqli_character_set_name($con));

// Clear the tables
$result = db_query('DELETE FROM url');
$result = db_query('DELETE FROM url_relationship');
$result = db_query('DELETE FROM script');
$result = db_query('DELETE FROM style');

function crawl_url($url) {
  global $con,$throttle,$successful_crawls,$blacklist,$whitelist;
  // Throttling is based on attempted crawls, not successful crawls
  $throttle--;
  if ($throttle < 0) return;
  $url_id = get_url_id($url);
  
  // If this URL isn't valid, abort
  if (!$url_id) return false;
  
  print "Processing $url\n"; 
 
  // We've already made sure it's valid so we shouldn't get here if it's invalid
  $html = crawler_get_file_contents($url);
  try {
  
  if (empty($html)) {
    print "Empty html found for $url\n";
  } else {
      // Add or Update title
      $title = '';
      try {
        $titleObj = $html->find("title",0);
        $title = addslashes(mysqli_real_escape_string($con,$titleObj->plaintext));
      } catch (Exception $e) { }
  
      $sql = "UPDATE url SET title = '" . $title . "' WHERE url_id=" . $url_id;
      $result = mysqli_query($con, $sql);
      if (!$result) {
        die('Error: ' . mysqli_error($con) . ' in ' . $sql);
      }
    
      // Obtain image for this page as well
      print "Obtaining screenshot for $title\n";
      $results = array();
      $filename = realpath(dirname(__FILE__)) . '/temp/page' . $url_id . '.png';
      exec('phantomjs rasterize.js ' . $url . ' ' . $filename, $results);    
      $result = array_pop($results);    
      if ($result == 'success') {
        print "Image generation successful\n";
        $sql = sprintf("UPDATE url SET has_image = 1 WHERE url_id=%d",$url_id);
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Error: ' . mysqli_error($con) . ' in ' );
        }
        unlink($filename);
      }  
  
      // Cycle through all the links on the page!
      print "Crawling $url\n"; 
      foreach($html->find("a") as $link){
        if ($link->href && strtolower(substr($link->href, 0,4)) == 'http') {
          $link->href = strip_hash($link->href);

          // Obtain the HREF
          $dest_url = addslashes($link->href);

          // Is the URL Whitelisted?
          if (count($whitelist) > 0 && preg_match('/(' . implode(')|(',$whitelist) . ')/i', parse_url($link->href,PHP_URL_HOST))) {
            print parse_url($link->href,PHP_URL_HOST) . " is not whitelisted.\n";
            continue;
          }
          if (count($blacklist) > 0 && preg_match('/(' . implode(')|(',$blacklist) . ')/i', parse_url($link->href,PHP_URL_HOST))) {
            print parse_url($link->href,PHP_URL_HOST) . " is blacklisted.\n";
            continue;
          }        
      
          // Obtain Link Text  
          print "Obtaining link text for {$link->href}\n";
          $linktext = addslashes(mysqli_real_escape_string($con,$link->plaintext));
            
          // Get the HREF's url_id
          // We'll take invalid URLs here as we'll want to track that
          $dest_url_id = get_url_id($dest_url);
      
          // Add the URL to link_relationships
          $sql = "INSERT INTO url_relationship (source_id, destination_id, linktext) VALUES ($url_id, $dest_url_id, '$linktext')";
          $result = mysqli_query($con,$sql);
          if (!$result) {    
            die('Error: ' . mysqli_error($con) . ' in ' . $sql);
          }
        }
      }
    
      foreach($html->find("script") as $script){
        if ($script->src) {
          // Obtain the SRC
          $script_src = addslashes($script->src);
          // Get the script contents
          $script_contents = crawler_get_file_contents(stripslashes($url), array('application/javascript','application/x-javascript','text/javascript'));
          // Take contents and create a hash      
          $hash = hash('sha256', $script_contents);
      
          // Add the URL script to the list of scripts on this page
          $sql = "INSERT INTO script (url_id, script_path, script_hash) VALUES ($url_id, '$script_src', '$hash')";
      
          $result = mysqli_query($con,$sql);
          if (!$result) {    
            die('Error: ' . mysqli_error($con) . ' in ' . $sql);
          }
        }
      }      

      // Get content for the page
      $results = array();
      $extract = 'java -jar BPExtract.jar ' . $url;
      print "Running extract: $extract\n" ;
      exec($extract, $results); 
      $content = implode("\n",$results);  

      $content = iconv(iconv_get_encoding('in_charset'), 'utf-8', $content);
      $content = addslashes($content);
      $content = mysqli_real_escape_string($con, $content);

      //print_r(iconv_get_encoding());
      print "Content retrieved: \n$content\n";
      if ($content != '') {        
        $sql = sprintf("UPDATE url SET content = '%s' WHERE url_id=%d", $content, $url_id);
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Error: ' . mysqli_error($con) . ' in ' . $sql);
        }
      }
    }
  }  
  catch (Exception $e) {
    debug_backtrace();
  }

  // Mark this URL as crawled
  set_as_crawled($url_id);
  
  $successful_crawls++;
  print "Crawls left: $throttle Crawls successful: $successful_crawls\n";
}

function strip_hash($url) {
  return strtok($url, "#");
}

function get_url_id($url) {
  global $con;
  $url_id = '';
  $url = addslashes($url);
    
  // Get the URL ID for the current page if there is one  
  $result = mysqli_query($con,"SELECT url_id FROM url WHERE url = '" . $url . "'");
  if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result)) {
      return $row['url_id'];
    }
  // if $url_id is still null
  } else {
    $sql = "INSERT INTO url (url) VALUES ('$url')";
    $result = mysqli_query($con,$sql);
    if ($result) {
      $url_id = mysqli_insert_id($con);
    } else {
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
  }

  // We now definitely have a $url_id
  return $url_id;
}

function crawler_get_file_contents($url, $contentType = array('text/html')) {
  $url = stripslashes($url);
  // the request
  $ch = curl_init(stripslashes($url));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_exec($ch);

  // Ensure the file exists/is accessible
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
    set_as_crawled(get_url_id($url), curl_getinfo($ch, CURLINFO_HTTP_CODE));
  print $url . " return code of " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    return false;
  }
  
  // Ensure it's the proper content type
  $valid = false;
  $html = false;
  foreach ($contentType as $ct) {
    if ((substr(strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE)),0,strlen($ct)) == $ct)) $valid = true;
  // Check for the content type being html as we'll need to use simple html dom to get the contents
  if ((substr(strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE)),0,9) == 'text/html')) $html = true;
  }
  if (!$valid) return false;
  
  // Get redirect if there is one
  $all_ch = curl_getinfo($ch);
  if ($all_ch['redirect_url'] != '') $url = $all_ch['redirect_url']; 
  
  if ($html) return file_get_html($url);
  return file_get_contents($url);
}

function set_as_crawled($url_id, $return_code = 200) {
  global $con;
  $sql = "UPDATE url SET crawled = 1, return_code = $return_code WHERE url_id=" . $url_id;
  $result = mysqli_query($con,$sql);
  if (!$result) {
    die('Error: ' . mysqli_error($con) . ' in ' . $sql);
  }
}

print "Starting at " . $initial_url;
// Add initial URL to the database
$url_id = get_url_id($initial_url);

// Now for the main event
while ($throttle > 0 && $depth > -1) {
  // Get list of uncrawled URLs
  $result = mysqli_query($con,"SELECT url FROM url WHERE crawled = 0 and crawlable = 1");
  while($row = mysqli_fetch_array($result)) {
  // Call crawl_url for each URL
    crawl_url(stripslashes($row['url']));
  }

  // Decrement depth
  $depth--;
}