<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);

			       $finalData=load($user_id,$con);
                   $myBookings=loadMyBookings($user_id,$con);
			       echo '{"status":1,"message":"loaded successfully","data":'.$finalData.',"my_bookings":'.$myBookings.'}';
                  
				}
				else
			     	echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}

//loading data 
function load($userId,$con)
{
			try
			{
				//getting flat data
				$state=$con->prepare("
					SELECT CONCAT( '[',

					  GROUP_CONCAT(JSON_OBJECT(

					 'id', user_id, 
					 'name', username, 
					 'image', image_url, 
					 'address', address, 
					 'token', token,  
					 'clinick_status', d_clinick_status,
					 'closing_reason', 	d_closing_reason,
					 'about',about_doctor,
					 'ratings',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=users.user_id),
					 'phones', (SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where user_id=users.user_id),

                      'work_days',(SELECT CONCAT( '[',
					 GROUP_CONCAT(JSON_OBJECT(
									'day',day,
									'start_time',start_time,
									'end_time',end_time
								))
					 
					 ,']') FROM doctor_days where doc_id=users.user_id)

					 ) )

					 
					 ,']' ) as data


					 FROM users WHERE status=1 and type=? and user_id in(select user_id from countries where country_name in(select country_name from countries where user_id=?) )");
				$state->execute(array('doctor',$userId)); 
				$data = $state->fetchAll();
			    if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
			   
			}
			catch(PDOException $ex)
			{
	   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
	}
	}

//loading  
function loadMyBookings($userId,$con)
	{
				$stateee=$con->prepare("SELECT CONCAT( '[',


				 GROUP_CONCAT(
				 JSON_OBJECT( 

				 'id',book_id,
				  'doc_id',doc_id, 
				  'doc_name',doc_name,
				 'doc_image',doc_image, 
				 'doc_address',doc_address,
				 'doc_token',doc_token,
				  'd_clinick_status',d_clinick_status, 
				  'd_closing_reason',d_closing_reason,
				 'booking_status',booking_status, 
				 'patient_name',patient_name,
				 'pain_desc',pain_desc,
				 'req_date',req_date,
				  'result_date',result_date,
				  'booking_final_date',booking_final_date,
				  'num_in_queue',num_in_queue,
				  'notes',notes	,
				 'booking_type',booking_type	
				 ))

				  ,']' ) as data FROM doc_bookings_v where user_id=?");
                $stateee->execute(array($userId)); 
				$data = $stateee->fetchAll();
              
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
 
	}
	