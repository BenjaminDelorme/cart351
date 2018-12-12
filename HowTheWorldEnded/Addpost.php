<?php
class MyDB extends SQLite3
{
   function __construct()
   {
      $this->open('db/billboardPosts.db');
   }
}
try
{
   $db = new MyDB();
}
catch(Exception $e)
{
    die($e);
}

//check if there has been something posted to the server to be processed
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
// need to process
 $title = $_POST['a_title'];
 $loc = $_POST['a_geo_loc'];
 $post = $_POST['a_post'];
 if($_FILES)
  {
    //echo "file name: ".$_FILES['filename']['name'] . "<br />";
    //echo "path to file uploaded: ".$_FILES['filename']['tmp_name']. "<br />";
   $fname = $_FILES['filename']['name'];
   move_uploaded_file($_FILES['filename']['tmp_name'], "images/".$fname);
   // NEW:: add into our db ....
   //The data from the text box is potentially unsafe; 'tainted'.
	 //We use the sqlite_escape_string.
	 //It escapes a string for use as a query parameter.
	//This is common practice to avoid malicious sql injection attacks.
	$title_es = $db->escapeString($title);
	$loc_es =$db->escapeString($loc);
	$post_es =$db->escapeString($post);
	// the file name with correct path
	$imageWithPath= "images/".$fname;
  // for the new column



  $queryInsert ="INSERT INTO posts(title, geoLoc, post, image)VALUES ( '$title_es','$loc_es','$post_es','$imageWithPath')";
  // again we do error checking when we try to execute our SQL statement on the db
	$ok1 = $db->exec($queryInsert);
  // NOTE:: error messages WILL be sent back to JQUERY sucess function .....
	if (!$ok1) {
    die("Cannot execute statement.");
    exit;
    }
    //send back success...
    echo "success";
    exit;
  }//FILES
}//POST
?>
