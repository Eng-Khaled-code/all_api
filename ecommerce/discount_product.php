<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $disId=filter_var($_POST['discount_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $productId=filter_var($_POST['product_id'],FILTER_SANITIZE_NUMBER_INT); 
			        
			       operations($disId,$type,$productId,$con);
			      
			      
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				
	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

	function operations($disId,$type,$productId,$con)
	{

		 try
			                {
			                	if($type=="add"){

			                	$state=$con->prepare("UPDATE  product set discount_id=? where id=?");
			                	$state->execute(array($disId,$productId));            
	                             }
	                             else if($type=="update"){

			                	$state=$con->prepare("UPDATE  product set discount_id=? where id=?");
			                	$state->execute(array($disId,$productId));            
	                             }
	                             else{
			                	$state=$con->prepare("UPDATE  product set discount_id=NULL where id=?");
			                	$state->execute(array($productId));            
	                             
	                             }
                              echo '{"status":1,"message":"successfully"}'; 

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
