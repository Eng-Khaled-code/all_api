<?php

	try
	{
				
				include '../config/config.php';

				//if($_SERVER['REQUEST_METHOD']=='POST')
				//{


			        $user_id=3;//filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);

                    
			       $finalData=load($user_id,$con);

			       echo '{"status":1,"message":"loaded successfully","data":'.$finalData.'}';

				//}
				//else
				//echo '{"status":0,"message":"you must came with post request","data":{}}';
				


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
					 'ratings',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=admin_id),
					 'date',req_date,
                     'like_count',(SELECT COUNT(*) from likes where liked_id=place_id),
                     'black_count',(SELECT COUNT(*) from black_places where liked_id=place_id),
					  'my_purchases',(SELECT CASE WHEN admin_id=? THEN 1 ELSE 0 END),
					 'is_fav',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM likes WHERE liked_id=place_id and from_id=? ),
					  'is_black',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM black_places WHERE liked_id=place_id and from_id=? ),
					  'is_discuss',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM discus WHERE discus.place_id=place_view.place_id and discus.user_id=? ),
					 'phones', (SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where phones.user_id=place_view.admin_id)

					 ))
					 
					 ,']' ) as data


					 FROM place_view WHERE main_admin_status=1  and  admin_id in(select user_id from countries where country_name in(select country_name from countries where user_id=?) )");
				$state->execute(array($userId,$userId,$userId,$userId,$userId)); 
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

	
