<?php

	try
	{
		
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{	
	       
	        $user_id=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT);
	        $finalData=load($user_id,$con);
	        $categories=loadCategories($con);
	        $discounts=loadDiscount($user_id,$con);
            $orders=loadOrders($user_id,$con);

            echo '{"status":1,"message":"loaded  successfully","data":'.$finalData.',"categories":'.$categories.',"discounts":'.$discounts.',"orders":'.$orders.'}';
        }
		else
	     echo '{"status":0,"message":"you must came with post request"}';
		


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//loading categories 
function loadCategories($con)
{
				$state=$con->prepare("
					SELECT CONCAT( '[\"',GROUP_CONCAT(name SEPARATOR '\",\"') , '\"]' )as data FROM category where status=1 and type='ecommerce' ");
				$state->execute(); 
				$data = $state->fetchAll();
			    
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
			
	}


	function loadDiscount($userId,$con)
	{
				$state=$con->prepare("SELECT CONCAT( '[', GROUP_CONCAT(JSON_OBJECT( 'id',id, 'name',name, 'discount_percentage',discount_percentage,'description',description, 'end_in',end_in, 'created_at',created_at, 'modified_at',modified_at, 'deleted_at',deleted_at, 'status',status )) ,']' ) as data FROM discount where admin_id=?");
                $state->execute(array($userId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
 
	}


//loading data 
function load($userId,$con)
{
			try
			{
				$state=$con->prepare("
					SELECT CONCAT( '[',

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


					 ) )

					 

					 ,']') as data FROM product_v where admin_id=?");
				$state->execute(array($userId)); 
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
function loadOrders($userId,$con)
	{
				$stateee=$con->prepare("SELECT CONCAT( '[',


				 GROUP_CONCAT(
				 JSON_OBJECT( 


				 'id',id, 
				 'created_at', created_at,
				 'address',(select JSON_OBJECT('id',shipping_address.id,'country',country,'city',city,'post_code',postal_code,'phone_1',phone_1,'phone_2',phone_2) from shipping_address where id=shipping_address_id),
				 'cart_data',(
                SELECT CONCAT( '[',

                      GROUP_CONCAT(JSON_OBJECT(

                      'cart_id',cart_v.id,'product_id',product_id,'product_name',cart_v.product_name,'product_image',cart_v.product_image,'product_quantity',product_quantity,'user_id',cart_v.user_id,'username',cart_v.username,'user_image',cart_v.image_url,'item_count',cart_v.quantity,'item_price',cart_v.item_price,'total_price',(cart_v.quantity*cart_v.item_price),'cart_status',cart_v.status

                      ))


                      ,']') FROM `cart_v` WHERE order_id=orders.id and admin_id =?)


				 ))

				  ,']' ) as data FROM orders where id in(select order_id from cart_v where admin_id=?)");
                $stateee->execute(array($userId,$userId)); 
				$data = $stateee->fetchAll();
              
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
 
	}