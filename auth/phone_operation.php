<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $phoneId=filter_var($_POST['phone_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $number=filter_var($_POST['number'],FILTER_SANITIZE_STRING); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);      
			      
			      if($type=="add")
			       add($number,$userId,$con);
			      elseif($type=="update")
			       update($number,$userId,$phoneId,$con);
			      else if($type=="delete")
			       delete($phoneId,$userId,$con);
			      else 
			      	load($userId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add phone
	function add($number,$userId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO phones(user_id,number) values (?,?)");
			                	$state->execute(array($userId,$number));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update phone
	function update($number,$userId,$phoneId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  phones set number=? where phone_id=?");
			                	$state->execute(array($number,$phoneId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update phone
	function delete($phoneId,$userId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  phones where phone_id=?");
			                	$state->execute(array($phoneId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($userId,$con)
	{
				$state=$con->prepare("SELECT CONCAT( '[',

					 GROUP_CONCAT(JSON_OBJECT(
									'user_id',user_id,
									'phone_id',phone_id,
									'number',number))
					 
					 ,']' ) as data  FROM phones where user_id=?");
                $state->execute(array($userId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}