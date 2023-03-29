<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{


			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $token=filter_var($_POST['token'],FILTER_SANITIZE_STRING); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $user_type=filter_var($_POST['user_type'],FILTER_SANITIZE_STRING);      
			        $email=filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); 
			        $password=sha1(filter_var($_POST['password'],FILTER_SANITIZE_STRING)); 
			        if($type=='log_in')
					  logIn($email,$password,$user_type,$token,$con);
			        else
			          logedInBefoure($user_id,$token,$con);
			        
			    }


				
				else
					echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}




//log in 
	function logIn($email,$password,$user_type,$token,$con)
	{
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			        {
			                try
			                {
			                   $state=$con->prepare("SELECT user_id,username,email,image_url,address,type,token,d_clinick_status,d_closing_reason,about_doctor,status,(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=users.user_id) as rating
 

			                    FROM users WHERE email=? and password=? and (type=? or type='full_access' ) LIMIT 1");
			                   $state->execute(array($email,$password,$user_type));          

	                           if($state->rowCount()>0)
	                            {
	                            	$row=$state->fetch();

	                            	if($row["status"]==1)
	                            	updateToken($token,$row,$con);
	                            	else
	                            	  echo '{"status":0,"message":"your account locked by admin","data":{}}';

	                            }
	                           else
	                             echo '{"status":0,"message":"email or password not valid","data":{}}';
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
							}
						}
			        else
			            echo '{"status":0,"message":"email not correct","data":{}}';

	}


//already loaged in
	function logedInBefoure($user_id,$token,$con)
	{



			      try
			        {
			        	$sta=$con->prepare("SELECT user_id,username,email,image_url,address,token,type,status,d_clinick_status,d_closing_reason,about_doctor,(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=users.user_id)as rating 
                              
			        		 FROM users WHERE user_id=? LIMIT 1");
			        	$sta->execute(array($user_id));          

			            if($sta->rowCount()>0)
			            {
			            	$row=$sta->fetch();
			            	if($row["status"]==1)
			            	updateToken($token,$row,$con);
			            	else
			            	  echo '{"status":0,"message":"your account locked by admin","data":{}}';

			            }
			            else
			              echo '{"status":0,"message":"sorry thier was a problem","data":{}}';
			             
			        }
			      catch(PDOException $ex)
				    {
						   echo '{"status":0,"message":"'.$ex->getMessage().'","data":{}}';
					}

	}



	//updatting token and returnning user data
	function updateToken($token,$dataList,$con)
	{

		try
	    {
	    	//echo $dataList['user_id'];
			$stat=$con->prepare("UPDATE users SET token =? WHERE user_id=?");
			$stat->execute(array($token,$dataList['user_id'])); 
	           
	         $data=
	           '{"user_id":'.$dataList['user_id']
	         .',"username":"'.$dataList['username']
	         .'","email":"'.$dataList['email']
	         .'","image_url":"'.$dataList['image_url']
	         .'","address":"'.$dataList['address']
	         .'","token":"'.$token
	         .'","status":'.$dataList['status']
	         .',"rating":"'.$dataList['rating']
	         .'","user_type":"'.$dataList['type']
	       .'","about_doctor":"'.$dataList['about_doctor']
	       .'","d_closing_reason":"'.$dataList['d_closing_reason']
	       .'","d_clinick_status":'.$dataList['d_clinick_status']

	         .'}';
			
			 echo '{"status":1,"message":"user data loaded successfully","data":'.$data.'}';

		}       
	   catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"'.$ex->getMessage().'","data":{}}';
		}
	}

