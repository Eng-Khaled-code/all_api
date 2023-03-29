<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $countryId=filter_var($_POST['country_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $country=filter_var($_POST['country'],FILTER_SANITIZE_STRING); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);      
			      
			      if($type=="add")
			       add($country,$userId,$con);
			      elseif($type=="update")
			       update($country,$userId,$countryId,$con);
			      else if($type=="delete")
			       delete($countryId,$userId,$con);
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


//add country
	function add($country,$userId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO countries(user_id,country_name) values (?,?)");
			                	$state->execute(array($userId,$country));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update country
	function update($country,$userId,$countryId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  countries set country_name=? where country_id=?");
			                	$state->execute(array($country,$countryId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update phone
	function delete($countryId,$userId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  countries where country_id=?");
			                	$state->execute(array($countryId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($userId,$con)
	{
				$state=$con->prepare("
                SELECT CONCAT( '[',
					 GROUP_CONCAT(JSON_OBJECT(
									'user_id',user_id,
									'country_id',country_id,
									'country',country_name))
					 
					 ,']') as data FROM countries where user_id=?");
                $state->execute(array($userId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))	
			    echo '{"status":1,"message":"successfully","data":[]}';
              else
              	echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';

	}