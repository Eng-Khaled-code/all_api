<?php

	try
    {
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
            
            $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
            $productId=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
            $status=filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
            $stopReason=filter_var($_POST['reason'],FILTER_SANITIZE_STRING);
            $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT);
            $adminToken=filter_var($_POST['admin_token'],FILTER_SANITIZE_STRING);

            if($type=="change status")
              changeStatus($productId,$adminId,$status,$stopReason,$adminToken,$con);
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


function changeStatus($productId,$adminId,$status,$stopReason,$adminToken,$con)
{
        try
        {
        	$state=$con->prepare("UPDATE  product set main_admin_id=? ,main_admin_status=?,stop_reason=? where id=?");
        	$state->execute(array($adminId,$status,$stopReason,$productId));            
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
				$state=$con->prepare("SELECT CONCAT( '[',

					 GROUP_CONCAT(JSON_OBJECT(
					 'product_id', id, 
					 'product_name', product_name, 
					 'description', description, 
					 'unit', unit, 
					 'category', name, 
					 'price', price, 
					 'quantity', quantity, 
					 'discount_id', discount_id, 
					 'dis_percentage', discount_percentage, 
					 'price_after_dis', price_after_dis, 
			         'admin_id', admin_id, 
			         'admin_image',image_url,
			         'admin_name',username,
			         'admin_token',pro_admin_token,
			         'ratings',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=admin_id),
					 'dis_status', status, 
					 'image_url', product_image,
					 'date',created_at, 
					 'dis_end_in',dis_end_in,
					 'main_admin_status',main_admin_status,
					 'stop_reason',stop_reason,
					 'like_count',(SELECT COUNT(*) from fav_product where product_id=id),
					 'black_count',(SELECT COUNT(*) from black_products where product_id=id)
))
					 
					 ,']' ) as data  FROM product_v where main_admin_id is null or main_admin_id=?");
                $state->execute(array($adminId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}
	