<?php

	try
    {
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
            
            $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
            $placeId=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
            $status=filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
            $stopReason=filter_var($_POST['reason'],FILTER_SANITIZE_STRING);
            $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT);
            $adminToken=filter_var($_POST['admin_token'],FILTER_SANITIZE_STRING);

            if($type=="change status")
              changeStatus($placeId,$adminId,$status,$stopReason,$adminToken,$con);
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


function changeStatus($placeId,$adminId,$status,$stopReason,$adminToken,$con)
{
        try
        {
        	$state=$con->prepare("UPDATE  place set main_admin_id=? ,main_admin_status=?,stop_reason=? where place_id=?");
        	$state->execute(array($adminId,$status,$stopReason,$placeId));            
        	load($adminId,$con);

        	//sent notification 
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}

}


	function load($adminId,$con)
	{
	 try
        {
        
				$state=$con->prepare("SELECT CONCAT( '[',

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
					 'main_admin_status',main_admin_status,
					 'stop_reason',stop_reason,
					 'status',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM discus WHERE discus.place_id=place_view.place_id and discus.status='buy'),
					 'date',req_date,
					 'discuss_list','[]',
                     'like_count',(SELECT COUNT(*) from likes where liked_id=place_id),
					 'black_count',(SELECT COUNT(*) from black_places where liked_id=place_id)	))
					 
					 ,']' ) as data  FROM place_view where main_admin_id is null or main_admin_id=?");
                $state->execute(array($adminId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	  }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}

	}
	