<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $cartId=filter_var($_POST['cart_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $productId=filter_var($_POST['product_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $quantity=filter_var($_POST['quantity'],FILTER_SANITIZE_STRING); 
			       
			      if($type=="add")
			       add($productId,$quantity,$userId,$con);
			      elseif($type=="update")
			       update($cartId,$quantity,$con);
			      else 
			       delete($cartId,$con);
			      
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add discount
	function add($productId,$quantity,$userId,$con)
	{
	



			                try
			                {
			                	$state=$con->prepare("INSERT INTO cart_item(product_id,user_id,quantity) values (?,?,?)");
			                	$state->execute(array($productId,$userId,$quantity));    
			                  echo '{"status":1,"message":"successfully"}';
        
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update 
	function update($cartId,$quantity,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  cart_item set quantity=? where id=?");
			                	$state->execute(array($quantity,$cartId));            
	                            echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function delete($cartId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  cart_item where id=?");
			                	$statek->execute(array($cartId));            
	                             echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
