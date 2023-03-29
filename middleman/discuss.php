<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_STRING);
			        $postId=filter_var($_POST['post_id'],FILTER_SANITIZE_STRING); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
                    
                    if($type=="discuss")
                    	add($userId,$postId,$con);
                    else
                    	delete($userId,$postId,$con);

				}
				else
					echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


    //add to discuss
	function add($userId,$postId,$con)
	{
            try
            {

	                $state=$con->prepare("INSERT INTO discus(user_id,place_id) VALUES (?,?)");
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



    //remove to favourate
	function delete($userId,$postId,$con)
	{
            try
            {

	                $state2=$con->prepare("DELETE FROM discus WHERE user_id=? and place_id=?");
	            	$state2->execute(array($userId,$postId));

	                if($state2->rowCount()>0)
	                  echo '{"status":1,"message":"successfully"}';
	                else
	                  echo '{"status":0,"message":"error"}';
             
	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
		
	}

