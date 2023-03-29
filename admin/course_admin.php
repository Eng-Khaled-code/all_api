<?php

	try
    {
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
            
            $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
            $courseId=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
            $status=filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
            $stopReason=filter_var($_POST['reason'],FILTER_SANITIZE_STRING);
            $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT);
            $adminToken=filter_var($_POST['admin_token'],FILTER_SANITIZE_STRING);

            if($type=="change status")
              changeStatus($courseId,$adminId,$status,$stopReason,$adminToken,$con);
            else 
              load($adminId,$con);	

		}
		else
			echo '{"status":0,"message":"you must came with post request"}';
		


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


function changeStatus($courseId,$adminId,$status,$stopReason,$adminToken,$con)
{
        try
        {
        	$state=$con->prepare("UPDATE  course set main_admin_id=? ,main_admin_status=?,main_admin_stop_reason=? where id=?");
        	$state->execute(array($adminId,$status,$stopReason,$courseId));            
        	load($adminId,$con);
             //send notification
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}

}


	function load($adminId,$con)
	{
				$state=$con->prepare("

					SELECT  ifnull(CONCAT( '[',

				 GROUP_CONCAT(JSON_OBJECT(
					'id',id,
					'name',name,
					'desc',description,
					'image_url',image,
					'date',datee,
					'status',status,
					'category',category,
					'like_count',like_count,
					'black_count',black_count,
                    'rate',rate,
					'user_count_rating',user_count_rating,
                    'user_count',user_count,
                     'video_count',  video_count,
                      'discount_id', discount_id, 
					 'dis_percentage', discount_percentage, 
					 'price_after_dis', price_after_dis, 
					  'dis_status', dis_status, 
                    'dis_end_in',end_in,
					  'main_admin_status',main_admin_status,
					  'main_admin_stop_reason',main_admin_stop_reason,
					   'user_id', user_id, 
			         'user_image',image_url,
			         'username',username,
			         'user_token',token,
			         'user_id',user_id,
			         'price',price
						))
					 
					 ,']' ),'[]') as data  FROM course_view where main_admin_id is null or main_admin_id=?");
                $state->execute(array($adminId)); 
				$data = $state->fetchAll();
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}
	