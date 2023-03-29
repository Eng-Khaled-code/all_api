<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
                    //add or update or delete
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING);  
			        $productId=filter_var($_POST['product_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $productName=filter_var($_POST['product_name'],FILTER_SANITIZE_STRING); 
                    $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $more_details=filter_var($_POST['desc'],FILTER_SANITIZE_STRING); 
			        $category=filter_var($_POST['category'],FILTER_SANITIZE_STRING); 
			        $price=filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_FLOAT);
			        $quantity=filter_var($_POST['quantity'],FILTER_SANITIZE_NUMBER_INT); 
 			        $unit=filter_var($_POST['unit'],FILTER_SANITIZE_STRING);      
                    $image_url=$_POST['image_name'];
			        $old_image_name=$_POST['old_image_name'];
			        $imageFile=base64_decode($_POST['base64']);

			        if($type=='add'){

			        	if(file_put_contents("images\\".$image_url,$imageFile)){
				           add($productName,$category,$adminId,$more_details,$price,$quantity,$unit,$image_url,$con);
				         }
				         else
				          Echo '{"status":0,"message" :"error while uploading image"}';

					 
			        }
			        elseif($type=="update"){
                 
                          if($old_image_name != "no"){ 
			        		Unlink("images\\".$old_image_name);

			        	if(file_put_contents("images\\".$image_url,$imageFile)){
                             update($productId,$productName,$category,$adminId,$more_details,$price,$quantity,$unit,$image_url,$con);
				          } else
				          Echo '{"status":0,"message" :"error while uploading image"}';

				      }
				          else if($old_image_name=="no")
				          {
				          	update($productId,$productName,$category,$adminId,$more_details,$price,$quantity,$unit,$image_url,$con);
				          }
				        
					  
			        }
                    else {

                    	Unlink("images\\".$image_url);
                    	delete($productId,$con);
                    }

				}
				else
					echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}



    //add
	function add($productName,$category,$adminId,$more_details,$price,$quantity,$unit,$image_url,$con){
			                try
			                {

			                	    $state=$con->prepare("INSERT INTO product(name,description,unit,price,
			                	    	quantity,admin_id,image_url,category_id) VALUES (?,?,?,?,?,?,?,(select id from category where name=?))");
			                	    $state->execute(array($productName,$more_details,$unit,$price,$quantity,$adminId,'http://192.168.43.109/all_api/ecommerce/images/'.$image_url,$category));       
	                            
	                           echo '{"status":1,"message":"added successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
	}


    //update 
	function update($productId,$productName,$category,$adminId,$more_details,$price,$quantity,$unit,$image_url,$con){
			                try
			                {

                               
			                	    $state=$con->prepare("UPDATE product SET name=?,description=?,unit=?,
			                	    	price=?,quantity=?,admin_id=?,image_url=?,category_id=(select id from category where name=?) WHERE id=?");
			                	    $state->execute(array($productName,$more_details,$unit,$price,$quantity,$adminId,'http://192.168.43.109/all_api/ecommerce/images/'.$image_url,$category,$productId));      
			             	            echo '{"status":1,"message":"updated successfully"}';

	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
						

	}


	function delete($productId,$con){

			                try
			                {

			                	    $state=$con->prepare("DELETE FROM product WHERE id=?");
			                	    $state->execute(array($productId));       
			             	            echo '{"status":1,"message":"deleted successfully"}';

	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					

	}


