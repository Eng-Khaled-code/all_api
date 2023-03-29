<?php

try
	{
				
	include '../config/config.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {

                $addOrUpdate=$_POST['addOrUpdatePhoto'];
			    $userId=$_POST['userId'];
			    $image_url=$_POST['image_name'];
			    $old_image_name=$_POST['old_image_name'];
			    $imageFile=base64_decode($_POST['base64']);

				if($addOrUpdate=="update")
				   Unlink("images\\".$old_image_name);


				if(file_put_contents("images\\".$image_url,$imageFile)){

				    load($userId,$image_url,$con);
				}
				else
				    Echo '{"status":0,"message" :"error"}';
		}
		else
			echo '{"status":0,"message":"you must came with post request"}';
		
		}

	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}

function load($userId,$image_url,$con)
	{

        $state=$con->prepare("UPDATE users set image_url='http://192.168.43.109/all_api/auth/images/".$image_url."' where user_id=?");
        $state->execute(array($userId)); 

		$state2=$con->prepare("SELECT JSON_OBJECT(
							'user_id',user_id,
							'username',username,
							'email',email,
	                        'address',address,
	                        'image_url',image_url,
	                        'token',token,
	                        'd_clinick_status',d_clinick_status,
	                        'd_closing_reason',d_closing_reason,
	                        'about_doctor',about_doctor,
	                        
	                        'rating',(SELECT CONCAT( '', AVG(rate),'') FROM ratings where user2_id=users.user_id)
						) as data  FROM users where user_id=?");
	    $state2->execute(array($userId)); 
		$data = $state2->fetchAll();
	  
	   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';

	}
 