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
   //echo ("Opened or created graffiti gallery data base successfully<br \>");
   $theQuery = 'CREATE TABLE posts (postID INTEGER PRIMARY KEY NOT NULL, title TEXT,geoLoc TEXT,post TEXT ,image TEXT)';
 $ok = $db ->exec($theQuery);
	// make sure the query executed
	if (!$ok)
	die($db->lastErrorMsg());
	// if everything executed error less we will arrive at this statement
	echo "Table posts created successfully<br \>";
}
catch(Exception $e)
{
   die($e);
}
?>
