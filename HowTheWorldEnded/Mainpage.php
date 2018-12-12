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
//  $criteria = $_POST['a_crit'];
  //  if($criteria == "ALL")
    //{
      $sql_select='SELECT * FROM posts';
      $result = $db->query($sql_select);
      if (!$result) die("Cannot execute query.");
  //  }
// get a row...
// MAKE AN ARRAY::
$res = array();
$i=0;
while($row = $result->fetchArray(SQLITE3_ASSOC))
{
  // note the result from SQL is ALREADy ASSOCIATIVE
 $res[$i] = $row;
 $i++;
}//end while
// endcode the resulting array as JSON
$myJSONObj = json_encode($res);
echo $myJSONObj;
 exit;
}//POST
?>
<!DOCTYPE html>
<html>
<head>
<title>Sample Retrieval USING JQUERY AND AJAX </title>
<!-- get JQUERY -->
  <script src = "libs/jquery-3.3.1.min.js"></script>
<!--set some style properties::: -->
<link rel="stylesheet" type="text/css" href="css/postStyle.css">
</head>
<body>
  <!-- <img id="wall" src="images/wall.png"></img> -->
  <!-- <img id="wall2" src="images/wall.png"></img> -->

  <div id="header">
    <!-- <img id="titleImg" src="images/titlesign.png"></img> -->
  <h1> "How the world ended"</h1>
  <h2> or how can we stop it</h2>
</div>
  <!-- <button type="button" id="SubmitButton"><a href="Addpost.php">Add a post</a></button> -->
   <a href="#" id="SubmitButton">Add a post</a>
   <a href="#" id="AboutButton">About</a>

   <div id="info">
   <h6>About this space</h6>
   <p> "How the world ended" is a place where people can share through the concept of a billboard differents events happening near them. By implementing the desired information, time and place, one can spread awarness of events they care about such as manifestations, meet-ups or even conventions.
   </br> </br> With this space, I hope to encourage people to share things they care about. Whether it's environmental or social, everyone has a word to say about the current situation in which we find ourselves so it's important to act and stand-up before it's too late.</p>
   </div>

<div id="submitpage"style="display:none" >
  <div class= "formContainer">
    <a href="#" id="close" style="color:black;">Close</a>
<!--form done using more current tags... -->
<form id="insertPost" action="" enctype ="multipart/form-data">
<!-- group the related elements in a form -->
<h3> SUBMIT A POST:</h3>
<fieldset>
<p><label>Title:</label><input type = "text" size="24"  maxlength = "40"  name = "a_title" required></p>
<p><label>Your Location:</label><input type = "text" size="24" maxlength = "20" name = "a_geo_loc" required></p>
<p><label>Post:</label><textarea type = "text" rows="15" cols="60" maxlength = "500" name = "a_post" required></textarea></p>
<p><label>Upload Image:</label> <input type ="file" name = 'filename' size=10/></p>
<p class = "sub"><input type = "submit" name = "submit" value = "Submit Post" id ="buttonS" /></p>
 </fieldset>
</form>
</div>
</div>



  <div id = "onPost" style="display:none">
    <h4 id="titlePost"></h4>
    <p id="locPost"></p>
    <p id="textPost"></p>
    <img id="imgPost" ></img>
  </div>

<div id="blur"></div>

<div id="billboard">
<!-- <img id="imgBG" src="images/billboard.png"></img> -->


<!-- NEW for the result -->
<div id = "result" ></div>

<script>
$(document).ready (function(){
  //  $("#retrieveFromGallery").submit(function(event) {
       //stop submit the form, we will post it manually. PREVENT THE DEFAULT behaviour ...
    //event.preventDefault();
     console.log("button clicked");
    // $("#onPost").hide();
    // let form = $('#retrieveFromGallery')[0];
     //let data = new FormData(form);
     $.ajax({
            type: "POST",
            enctype: 'text/plain',
            url: "Mainpage.php",
            data: "none",
            processData: false,//prevents from converting into a query string
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (response) {
            console.log(response);
            //use the JSON .parse function to convert the JSON string into a Javascript object
            let parsedJSON = JSON.parse(response);
            console.log(parsedJSON);
            displayResponse(parsedJSON);
           },
           error:function(){
          console.log("error occurred");
        }
      });
   //});

   // validate and process form here
    function displayResponse(theResult){
      // theResult is AN ARRAY of objects ...
      for(let i=0; i< theResult.length; i++)
      {
      // get the next object
      let currentObject = theResult[i];
      let container = $('<div>').addClass("outer");
     container.attr("id",currentObject["postID"]);
      container.attr("data-title",currentObject["title"]);
        container.attr("data-loc",currentObject["geoLoc"]);
        container.attr("data-post",currentObject["post"]);
        container.attr("data-image",currentObject["image"]);

      let contentContainer = $('<div>').addClass("content");
      let containerMain = $('<div>').addClass("outerMain");
      let contentContainerMain = $('<div>').addClass("contentMain");
      // go through each property in the current object ....
      for (let property in currentObject) {
        if(property ==="image" && currentObject[property]!=="images/"){
         console.log(currentObject[property]);
        let imgMain = $("<img>").addClass("imagePost");
        let img = $("<img>").addClass("imgIcon");
         $(imgMain).attr('src',currentObject[property]);
         $(img).attr('src',"images/imgIcon.png");
          $(img).appendTo(contentContainer);
          $(imgMain).appendTo(contentContainerMain);
        }
        else if(property==="title"){
          let title = $('<h4>').addClass("postTitle");
          let titleMain = $('<h4>').addClass("postTitleMain");
          let pinImg = $("<img>").addClass("pin");
         $(pinImg).attr('src',"images/pin.png");
          $(title).text(currentObject[property]);
          $(titleMain).text(currentObject[property]);
          //property+":" +
           $(pinImg).appendTo(contentContainer);
            $(title).appendTo(contentContainer);
            $(titleMain).appendTo(contentContainerMain);
        }
        else if(property==="geoLoc"){
          let location = $('<p>').addClass("postLocation");
          let locationMain = $('<p>').addClass("postLocationMain");
          $(location).text("Posted in "+ currentObject[property]);
          $(locationMain).text("Posted in "+ currentObject[property]);
            $(location).appendTo(contentContainer);
            $(locationMain).appendTo(contentContainerMain);
        }
        else if(property==="post"){
          console.log(currentObject["postID"]);
          let para = $('<p>').addClass("Post");
          let paraMain = $('<p>').addClass("Post");
          let testSubstring = currentObject[property].substr(0,200)+"...";
          $(para).text(testSubstring);
          $(paraMain).text(currentObject[property]);
          //property+":" +
            $(para).appendTo(contentContainer);
          //  $(paraMain).appendTo(contentContainerMain);
        }

      }
      $(contentContainer).appendTo(container);
       $(container).on("click", function(){

       $("#titlePost").html($(this).attr("data-title"));
       $("#locPost").html($(this).attr("data-loc"));
       $("#textPost").html($(this).attr("data-post"));
       //let test $("<img>")=
       //$("#imgPost").html($(this).attr("data-image"));
       $("#imgPost").attr("src",$(this).attr("data-image"));
      // $(containerMain).appendTo("#onPost");
     });

    //  })
      $(container).appendTo("#result");

    //  $(contentContainerMain).appendTo(containerMain);
      //$(containerMain).appendTo("#onPost");

    }

  }


  $("#info").click(function(){
    $("#blur").fadeOut("fast");
    $("#info").fadeOut("fast");
  })
  $("#AboutButton").click(function(){
    $("#blur").fadeIn("fast");
   $("#info").fadeIn("fast");


  })


  $("#result").click(function(){
    //$("#result").push("#onPost");
    $("#blur").fadeIn("fast");
   $("#onPost").fadeIn("fast");


  })
  $("#onPost").click(function(){
    //$("#result").push("#onPost");
    $("#blur").fadeOut("fast");
    $("#onPost").fadeOut("fast");


  })


  // $("#SubmitButton").click(function(){
  //   //$("#result").push("#onPost");
  //  $("#submitpage").show();
  //
  // })

  // this initializes the dialog (and uses some common options that I do)

  // next add the onclick handler
  $("#SubmitButton").click(function() {

              // $("#submitpage").toggle( "slide" );
              // });
              $( "#submitpage" ).slideDown( "slow", function() {
    // Animation complete.
  });
  });


    $("#close").click(function() {

      $( "#submitpage" ).slideUp( "slow", function() {
// Animation complete.
});
      });

// http://jsfiddle.net/RBJ9R/2316/
// https://jsfiddle.net/bac8qdq1/13/


// for submit a post
$("#insertPost").submit(function(event) {

  $( "#submitpage" ).slideUp( "slow", function() {
// Animation complete.
});

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
  location.reload();
});

});
</script>
</div>
</body>
</html>
