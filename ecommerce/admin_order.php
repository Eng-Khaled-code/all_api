<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $productId=filter_var($_POST['product_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $cartId=filter_var($_POST['cart_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $quantity=filter_var($_POST['quantity'],FILTER_SANITIZE_NUMBER_INT); 
			       

			      if($type=="accept")
			       accept($productId,$cartId,$quantity,$con);
			      else
			      	refuse($cartId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

	function accept($productId,$cartId,$quantity,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  product set quantity=quantity-? where id=?;UPDATE cart_item set status=1 where id=?
			                		");
			                	$state->execute(array($quantity,$productId,$cartId));
			                	            
	                             echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function refuse($cartId,$con)
	{
	
			                try
			                {

			                	  $state=$con->prepare("UPDATE cart_item set status=2 where id=?");
			                	  $state->execute(array($cartId));            
	                              echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}