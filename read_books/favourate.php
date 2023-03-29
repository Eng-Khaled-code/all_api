<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_STRING);
			        $bookId=filter_var($_POST['book_id'],FILTER_SANITIZE_STRING); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
                    
                   if($type=="like")
                    	add($userId,$bookId,'fav_books',$con);
                   else if($type=="dislike")
                    	delete($userId,$bookId,'fav_books',$con);
                   else if($type=="black")
                    	add($userId,$bookId,'black_books',$con);
                   else if($type=="unblack")
                    	delete($userId,$bookId,'black_books',$con);
				}
				else
					echo '{"status":0,"message":"you must came with post request"}';

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


    //add to favourate
	function add($userId,$bookId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("INSERT INTO ".$tableName." (book_id,user_id) VALUES (?,?)");
	            	$state->execute(array($bookId,$userId));

	                if($state->rowCount()>0)
	                  echo '{"status":1,"message":"successfully"}';
	                else
	                  echo '{"status":0,"message":"error"}';
             
	        }
	        catch(PDOException $ex)
		    {
				      echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
		
	}

    //remove to favourate
	function delete($userId,$bookId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("DELETE FROM ".$tableName." WHERE user_id=? and book_id=?");
	            	$state->execute(array($userId,$bookId));

	                if($state->rowCount()>0)
	                  echo '{"status":1,"message":"successfully"}';
	                else
	                  echo '{"status":0,"message":"error"}';
             
	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
		
	}
