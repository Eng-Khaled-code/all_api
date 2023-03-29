<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $orderId=filter_var($_POST['order_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $addressId=filter_var($_POST['adress_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $total_price=filter_var($_POST['total_price'],FILTER_SANITIZE_STRING); 
			        $totalItemCount=filter_var($_POST['total_item_count'],FILTER_SANITIZE_NUMBER_INT); 
                    $cartAsList=json_decode($_POST['cart_list']);

			      if($type=="add")
			       add($addressId,$total_price,$totalItemCount,$cartAsList,$userId,$con);
			      else if($type=="delete")
			      	delete($orderId,$con);
			      else
			      	changeAddress($orderId,$addressId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add 
	function add($addressId,$total_price,$totalItemCount,$cartAsList,$userId,$con)
	{
	



	    try
	    {
	    	$state=$con->prepare("INSERT INTO orders(shipping_address_id,total_price,total_item_count,user_id) values (?,?,?,?)");
	    	$state->execute(array($addressId,$total_price,$totalItemCount,$userId)); 

	    	if(!empty($con->lastInsertId()))
	    		{

	    			$orderid=$con->lastInsertId();
                     

                foreach($cartAsList as $cartId => $item_price) {

                 
			    	$statem=$con->prepare("UPDATE cart_item set order_id=? ,item_price=? where id=?");
			    	$statem->execute(array($orderid,$item_price,$cartId)); 

                  }

	                echo '{"status":1,"message":"successfully"}';


	    		}
	    		else
	    		{   
	                echo '{"status":0,"message":"error"}';
	            }

	         
	    }
	    catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}


	function delete($orderId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  orders where id=?");
			                	//automatecally deletes cart_items that belongs to this order
			                	$statek->execute(array($orderId));            
	                             echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
function changeAddress($orderId,$addressId,$con)
	{
	
            try
            {
            	$state=$con->prepare("UPDATE  orders set shipping_address_id=? where id=?");
            	$state->execute(array($addressId,$orderId));            
                echo '{"status":1,"message":"successfully"}';

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
	
	}