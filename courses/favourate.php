<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);
			        $courseId=filter_var($_POST['course_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
                    
                   if($type=="like")
                    	add($userId,$courseId,'fav_courses',$con);
                   else if($type=="dislike")
                    	delete($userId,$courseId,'fav_courses',$con);
                   else if($type=="black")
                    	add($userId,$courseId,'black_courses',$con);
                   else if($type=="unblack")
                    	delete($userId,$courseId,'black_courses',$con);
				}
				else
					echo '{"status":0,"message":"you must came with post request"}';

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


    //add to favourate
	function add($userId,$courseId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("INSERT INTO ".$tableName." (course_id,user_id) VALUES (?,?)");
	            	$state->execute(array($courseId,$userId));

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
	function delete($userId,$courseId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("DELETE FROM ".$tableName." WHERE user_id=? and course_id=?");
	            	$state->execute(array($userId,$postId));

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
