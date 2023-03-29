<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);
			       $finalData=load($user_id,$con);

			       echo '{"status":1,"message":"loaded successfully","data":'.$finalData.'}';

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

					 'place_id', place_id, 
					 'address', address, 
					 'its_rouf_num', its_rouf_num, 
					 'type', type, 
					 'size', size, 
					 'metre_price', metre_price, 
					 'total_price', total_price, 
					 'operation', operation, 
					 'more_details', more_details, 
			         'admin_id', admin_id, 
					 'admin_name', admin_name, 
					 'image_url', admin_image,
					 'admin_token',admin_token,
					 'main_admin_status',main_admin_status,
					 'stop_reason',stop_reason,
					 'ratings',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=admin_id),
					 'status',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM discus WHERE discus.place_id=place_view.place_id and discus.status='buy'),
					 'date',req_date, 
					  'like_count',(SELECT COUNT(*) from likes where liked_id=place_id),
					  'black_count',(SELECT COUNT(*) from black_places where liked_id=place_id),

					 'discuss_list',(SELECT CONCAT( '[',

					 GROUP_CONCAT(JSON_OBJECT(
					 'user_id',user_id,
					 'username',username,
					 'image_url',image_url,
					 'token',token,
					 'date',discuss_view.req_date,
					 'status',discuss_view.status,
					  'phones', (SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where user_id=discuss_view.user_id)

					 ) )

					 ,']') FROM discuss_view where discuss_view.place_id=place_view.place_id)
					 ))
					 
					 ,']' ) as data


					 FROM place_view WHERE admin_id=?");
				$state->execute(array($userId)); 
				$data = $state->fetchAll();
			    return $data[0]['data'];
			   
			}
			catch(PDOException $ex)
			{
	   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
	}
	}

	
