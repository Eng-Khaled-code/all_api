<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);

			       $finalData=load($user_id,$con);
                   $categories=loadCategories($con);
                   $address=loadAddress($user_id,$con);
                   $orders=loadOrders($user_id,$con);
			       echo '{"status":1,"message":"loaded successfully","data":'.$finalData.',"categories":'.$categories.',"address":'.$address.',"orders":'.$orders.'}';
                  
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
					SELECT ifnull(CONCAT( '[',

					  GROUP_CONCAT(JSON_OBJECT(

					 'product_id', id, 
					 'product_name', product_name, 
					 'description', description, 
					 'unit', unit, 
					 'category', name, 
					 'price', price, 
					 'quantity', quantity,
					 'image_url', product_image,
					 'date',created_at, 
					 'discount_id', discount_id, 
					 'dis_percentage', discount_percentage, 
					 'price_after_dis', price_after_dis, 
					 'dis_status', status,			
					 'dis_end_in',dis_end_in,
					 'admin_id', admin_id, 
			         'admin_image',image_url,
			         'admin_name',username,
			         'admin_token',pro_admin_token,
			         'black_count',(SELECT COUNT(*) from black_products where product_id=id),
			         'ratings',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=admin_id),
					  'like_count',(SELECT COUNT(*) from fav_product where product_id=id),
                      'is_fav',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM fav_product WHERE product_id=id and user_id=? ),
                      'is_black',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM black_products WHERE product_id=id and user_id=? ),
                      'cart_data',(SELECT JSON_OBJECT('cart_id',cart_item.id,'item_count',cart_item.quantity,'item_price',item_price,
                      'total_price',
                        (CASE WHEN product_v.status= 1 THEN cart_item.quantity*product_v.price_after_dis ELSE cart_item.quantity*product_v.price END)
                      ) FROM cart_item WHERE product_id=product_v.id and user_id=? and order_id is null )

					 ) )

					 
					 ,']' ),'[]') as data


					 FROM product_v WHERE main_admin_status=1 and quantity>0 and admin_id in(select user_id from countries where country_name in(select country_name from countries where user_id=?) )");
				$state->execute(array($userId,$userId,$userId,$userId)); 
				$data = $state->fetchAll();
			 
			   return $data[0]['data'];
			   
			}
			catch(PDOException $ex)
			{
	   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
	}
	}

//loading categories 
function loadCategories($con)
{
				$state=$con->prepare("
					SELECT ifnull(CONCAT( '[\"',GROUP_CONCAT(name SEPARATOR '\",\"') , '\"]' ),'[]')  as data FROM category where status =1  and type='ecommerce' ");
				$state->execute(); 
				$data = $state->fetchAll();
			   return $data[0]['data'];
			
	}
	

//loading  
function loadAddress($userId,$con)
	{
				$stateee=$con->prepare("SELECT ifnull(CONCAT( '[', GROUP_CONCAT(JSON_OBJECT( 'id',id, 'country',country, 'city',city,'post_code',postal_code, 'phone_1',phone_1, 'phone_2',phone_2)) ,']' ),'[]')  as data FROM shipping_address where user_id=?");
                $stateee->execute(array($userId)); 
				$data = $stateee->fetchAll();
			   return $data[0]['data'];
 
	}
	


//loading  
function loadOrders($userId,$con)
	{
				$stateee=$con->prepare("SELECT ifnull(CONCAT( '[',


				 GROUP_CONCAT(
				 JSON_OBJECT( 


				 'id',id, 'created_at',created_at, 'total_price',total_price,
				 'total_item_count',total_item_count, 
				 'address',(select JSON_OBJECT('id',shipping_address.id,'country',country,'city',city,'post_code',postal_code,'phone_1',phone_1,'phone_2',phone_2) from shipping_address where id=shipping_address_id),
				 'cart_data',(
                SELECT CONCAT( '[',

                      GROUP_CONCAT(JSON_OBJECT(

                      'cart_id',cart_v.id,'product_name',cart_v.product_name,'product_image',cart_v.product_image,'product_admin',cart_v.product_admin,'admin_image',cart_v.admin_image,'item_count',cart_v.quantity,'item_price',cart_v.item_price,'total_price',(cart_v.quantity*cart_v.item_price),'cart_status',cart_v.status

                      ))


                      ,']') FROM `cart_v` WHERE order_id=orders.id)


				 ))

				  ,']' ),'[]')  as data FROM orders where user_id=?");
                $stateee->execute(array($userId)); 
				$data = $stateee->fetchAll();
              
			   return $data[0]['data'];
 
	}
	