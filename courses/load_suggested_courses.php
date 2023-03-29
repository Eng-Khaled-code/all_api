<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);

			       $finalData=load($user_id,$con);
                   $categories=loadCategories($con);
                     echo '{"status":1,"message":"loaded successfully","data":'.$finalData.',"categories":'.$categories.'}';
                }
				else
			     	echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}

function load($userId,$con)
{
		$state=$con->prepare("

					SELECT  ifnull(CONCAT( '[',

				 GROUP_CONCAT(JSON_OBJECT(
					'id',id,
					'name',name,
					'desc',description,
					'image_url',image,
					'date',datee,
					'category',category,
					'like_count',like_count,
					 'is_fav',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM fav_courses WHERE course_id=id and user_id=? ),
                      'is_black',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM black_courses WHERE course_id=id and user_id=? ),
                      is_purechased,(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM course_users WHERE course_id=id and user_id=? ),
                    'rate',rate,
					'user_count_rating',user_count_rating,
                     'video_count',  video_count,
                      'discount_id', discount_id, 
					 'dis_percentage', discount_percentage, 
					 'price_after_dis', price_after_dis, 
					  'dis_status', dis_status, 
                    'dis_end_in',end_in,
					 'user_id', user_id, 
			         'user_image',image_url,
			         'username',username,
			         'user_token',token,
			         'price',price
						))
					 
					 ,']' ),'[]') as data  FROM course_view WHERE main_admin_status=1 and user_id in(select user_id from countries where country_name in(select country_name from countries where user_id=?) )");

		$state->execute(array($userId,$userId,$userId));
				$data = $state->fetchAll();
			 
			   return $data[0]['data'];

}



//loading categories 
function loadCategories($con)
{
				$state=$con->prepare("
					SELECT ifnull(CONCAT( '[\"',GROUP_CONCAT(name SEPARATOR '\",\"') , '\"]' ),'[]')  as data FROM category where status =1  and type='course' ");
				$state->execute(); 
				$data = $state->fetchAll();
			   return $data[0]['data'];
			
	}
