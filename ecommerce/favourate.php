<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_STRING);
			        $postId=filter_var($_POST['post_id'],FILTER_SANITIZE_STRING); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
                    
                   if($type=="like")
                    	add($userId,$postId,'fav_product',$con);
                   else if($type=="dislike")
                    	delete($userId,$postId,'fav_product',$con);
                   else if($type=="black")
                    	add($userId,$postId,'black_products',$con);
                   else if($type=="unblack")
                    	delete($userId,$postId,'black_products',$con);
				}
				else
					echo '{"status":0,"message":"you must came with post request"}';

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


    //add to favourate
	function add($userId,$postId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("INSERT INTO ".$tableName." (product_id,user_id) VALUES (?,?)");
	            	$state->execute(array($postId,$userId));

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
	function delete($userId,$postId,$tableName,$con)
	{
            try
            {

	                $state=$con->prepare("DELETE FROM ".$tableName." WHERE user_id=? and product_id=?");
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
