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
<!DOCTYPE html>
<html>
<head>
<title>Submit a post </title>
<!-- get JQUERY -->
  <script src = "libs/jquery-3.3.1.min.js"></script>
<!--set some style properties::: -->
<link rel="stylesheet" type="text/css" href="css/postStyle.css">
</head>
<body id="SubmitPage">

  <!-- NEW for the result -->

<div id = "result"></div>

<div class= "formContainer">
<!--form done using more current tags... -->
<form id="insertPost" action="" enctype ="multipart/form-data">
<!-- group the related elements in a form -->
<h3> SUBMIT A POST:</h3>
<fieldset>
<p><label>Title:</label><input type = "text" size="24"  maxlength = "40"  name = "a_title" required></p>
<p><label>Your Location:</label><input type = "text" size="24" maxlength = "20" name = "a_geo_loc" required></p>
<p><label>Post:</label><textarea type = "text" rows="10" cols="60" maxlength = "500" name = "a_post" required></textarea></p>
<p><label>Upload Image:</label> <input type ="file" name = 'filename' size=10/></p>
<p class = "sub"><input type = "submit" name = "submit" value = "submit my info" id ="buttonS" /></p>
 </fieldset>
</form>
</div>
<script>
$(document).ready (function(){
    $("#insertPost").submit(function(event) {
       //stop submit the form, we will post it manually. PREVENT THE DEFAULT behaviour ...
    event.preventDefault();

     console.log("button clicked");
     let form = $('#insertPost')[0];
     let data = new FormData(form);
     $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "Addpost.php",
            data: data,
            processData: false,//prevents from converting into a query string
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (response) {
            console.log(response);
           },
           error:function(){
          console.log("error occurred");
        }
      });
   });

});
</script>
</body>
</html>
