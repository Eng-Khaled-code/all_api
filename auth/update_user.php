<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $key1=filter_var($_POST['key'],FILTER_SANITIZE_STRING); 
			        $value1=filter_var($_POST['value'],FILTER_SANITIZE_STRING); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_STRING);      
			      
			       update($key1,$value1,$userId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add user
	function update($key,$value,$userId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE users SET ".$key."='".$value."'  where user_id=?");
			                	$state->execute(array($userId));          

	                            	$state2=$con->prepare("
									SELECT JSON_OBJECT(
									'user_id',user_id,
									'username',username,
									'email',email,
									'address',address,
									'image_url',image_url,
									'token',token,
									'd_clinick_status',d_clinick_status,
	                        'd_closing_reason',d_closing_reason,
	                        'about_doctor',about_doctor,
                                     'rating',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=users.user_id)
									) as data 
									from users WHERE user_id=?");
									$state2->execute(array($userId)); 
									$data = $state2->fetchAll();
						                  
              if(empty($data[0]['data']))	
			    echo '{"status":1,"message":"user updated successfully","data":[]}';
              else
              	echo '{"status":1,"message":"user updated successfully","data":'.$data[0]['data'].'}';   

	                           
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
