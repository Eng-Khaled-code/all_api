<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $dayId=filter_var($_POST['day_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $dayName=filter_var($_POST['day'],FILTER_SANITIZE_STRING); 
			        $startTime=filter_var($_POST['start_time'],FILTER_SANITIZE_STRING); 
			        $endTime=filter_var($_POST['end_time'],FILTER_SANITIZE_STRING); 
			        $doctorId=filter_var($_POST['doc_id'],FILTER_SANITIZE_NUMBER_INT);      
			      
			      if($type=="add")
			       add($dayName,$doctorId,$startTime,$endTime,$con);
			      elseif($type=="update")
			       update($dayName,$doctorId,$dayId,$startTime,$endTime,$con);
			      else if($type=="delete")
			       delete($doctorId,$dayId,$con);
			      else 
			      	load($doctorId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add country
	function add($dayName,$doctorId,$startTime,$endTime,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO doctor_days(doc_id,day,start_time,end_time) values (?,?,?,?)");
			                	$state->execute(array($doctorId,$dayName,$startTime,$endTime));            
			                	load($doctorId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update country
	function update($dayName,$doctorId,$dayId,$startTime,$endTime,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  doctor_days set day=? ,start_time=?,end_time=? where id=?");
			                	$state->execute(array($dayName,$startTime,$endTime,$dayId));            
			                	load($doctorId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update phone
	function delete($doctorId,$dayId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  doctor_days where id=?");
			                	$state->execute(array($dayId));            
			                	load($doctorId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($doctorId,$con)
	{
				$state=$con->prepare("
                SELECT CONCAT( '[',
					 GROUP_CONCAT(JSON_OBJECT(
					                'id',id,
									'doc_id',doc_id,
									'day',day,
									'start_time',start_time,
									'end_time',end_time

								))
					 
					 ,']') as data FROM doctor_days where doc_id=?");
                $state->execute(array($doctorId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))	
			    echo '{"status":1,"message":"successfully","data":[]}';
              else
              	echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';

	}