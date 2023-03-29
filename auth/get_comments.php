<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $user1Id=filter_var($_POST['user1_id'],FILTER_SANITIZE_STRING);    
			        $user2Id=filter_var($_POST['user2_id'],FILTER_SANITIZE_STRING);      
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING);      
			  		$comment=filter_var($_POST['comment'],FILTER_SANITIZE_STRING);      
			        $rate=filter_var($_POST['rate'],FILTER_SANITIZE_STRING);      
			        $userType=filter_var($_POST['user_type'],FILTER_SANITIZE_STRING);      
                  
                  if($type=="add")
			       add($user1Id,$user2Id,$comment,$userType,$rate,$con);
			      elseif($type=="update")
			       update($user1Id,$user2Id,$comment,$userType,$rate,$con);
			      else if($type=="delete")
			       delete($user1Id,$user2Id,$userType,$con);
			      else 
			       load($user1Id,$user2Id,$userType,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add rate
	function add($user1Id,$user2Id,$comment,$userType,$rate,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO ratings(user1_id,user2_id,rate,comment) values (?,?,?,?)");
			                	$state->execute(array($user1Id,$user2Id,$rate,$comment));            
			                	load($user1Id,$user2Id,$userType,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update rate
	function update($user1Id,$user2Id,$comment,$userType,$rate,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  ratings set comment=?,rate=? where user1_id=? and user2_id=?");
			                	$state->execute(array($comment,$rate,$user1Id,$user2Id));            
			                	load($user1Id,$user2Id,$userType,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//delete rate
	function delete($user1Id,$user2Id,$userType,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  ratings where user1_id=? and user2_id=?");
			                	$state->execute(array($user1Id,$user2Id));            
			                	load($user1Id,$user2Id,$userType,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
	

function load($user1Id,$user2Id,$userType,$con)
	{
				$state;
                if($userType=="user")
                {
		       		$state=$con->prepare("
                
                SELECT CONCAT( '[',
					 GROUP_CONCAT(JSON_OBJECT(
					                'user1_id',user1_id,
					                'user2_id',user2_id,
									'username',username2,
									'image_url',image_url2,
									'email',email2,
                                     'rate',rate,
                                     'comment',comment,
                                     'date',date
									))
					 
					 ,']') as data FROM rate_view where user1_id=?");
		       		$state->execute(array($user1Id));

		       	}
		       		else
		       		{

		       		$state=$con->prepare("
                
                SELECT CONCAT( '[',
					 GROUP_CONCAT(JSON_OBJECT(
					                'user_id',user2_id,
									'username',username1,
									'image_url',image_url1,
									'email',email1,
                                     'rate',rate,
                                     'comment',comment,
                                     'date',date
									))
					 
					 ,']') as data FROM rate_view where user2_id=?");
		       		$state->execute(array($user2Id));
		       		 }
                
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))	
			    echo '{"status":1,"message":"successfully","data":[]}';
              else
              	echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';

	}