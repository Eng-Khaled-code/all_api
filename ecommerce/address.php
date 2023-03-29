<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $id=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT); 
			        $postCode=filter_var($_POST['post_code'],FILTER_SANITIZE_STRING); 
			        $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $country=filter_var($_POST['country'],FILTER_SANITIZE_STRING); 
			       	$city=filter_var($_POST['city'],FILTER_SANITIZE_STRING); 
                    $phone1=filter_var($_POST['phone_1'],FILTER_SANITIZE_STRING); 
			       	$phone2=filter_var($_POST['phone_2'],FILTER_SANITIZE_STRING); 

			      if($type=="add")
			       add($country,$city,$userId,$postCode,$phone1,$phone2,$con);
			      elseif($type=="update")
			       update($id,$country,$city,$postCode,$phone1,$phone2,$con);
			      else 
			       delete($id,$con);
			      
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add discount
	function add($country,$city,$userId,$postCode,$phone1,$phone2,$con)
	{
	



			                try
			                {
			                	$state=$con->prepare("INSERT INTO shipping_address(country,city,user_id,postal_code,phone_1,phone_2) values (?,?,?,?,?,?)");
			                	$state->execute(array($country,$city,$userId,$postCode,$phone1,$phone2));    
			                  echo '{"status":1,"message":"successfully"}';
        
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update 
	function update($id,$country,$city,$postCode,$phone1,$phone2,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("UPDATE  shipping_address set country=? ,city=? ,
			                	postal_code=?,phone_1=?,phone_2=? where id=?");
			                	$state->execute(array($country,$city,$postCode,$phone1,$phone2,$id));            
	                            echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function delete($id,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  shipping_address where id=?");
			                	$statek->execute(array($id));            
	                             echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
