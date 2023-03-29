<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['doc_id'],FILTER_SANITIZE_NUMBER_INT);
                   $myBookings=loadMyBookings($user_id,$con);
			       echo '{"status":1,"message":"loaded successfully","data":'.$myBookings.'}';
                  
				}
				else
			     	echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}

function loadMyBookings($userId,$con)
	{
				$stateee=$con->prepare("SELECT CONCAT( '[',
				 GROUP_CONCAT(
				 JSON_OBJECT( 
				 'id',book_id,
				  'user_id',user_id, 
				  'username',username,
				 'image_url',image_url, 
				 'user_token',user_token,
				 'booking_status',booking_status, 
				 'patient_name',patient_name,
				 'pain_desc',pain_desc,
				 'req_date',req_date,
				  'response_date',result_date,
				  'booking_final_date',booking_final_date,
				  'num_in_queue',num_in_queue,
				  'notes',notes	,
				 'booking_type',booking_type	
				 ))

				  ,']' ) as data FROM doc_bookings_v where doc_id=?");
                $stateee->execute(array($userId)); 
				$data = $stateee->fetchAll();
              
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
 
	}
	