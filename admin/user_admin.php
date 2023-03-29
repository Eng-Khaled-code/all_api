<?php

	try
    {
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
            
            $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
            $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);
	        $address=filter_var($_POST['address'],FILTER_SANITIZE_STRING); 
	        $username=filter_var($_POST['username'],FILTER_SANITIZE_STRING); 
	        $userType=filter_var($_POST['user_type'],FILTER_SANITIZE_STRING);      
	        $email=filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); 
	        $password=sha1(filter_var($_POST['password'],FILTER_SANITIZE_STRING));
	        $phone=filter_var($_POST['phone'],FILTER_SANITIZE_STRING); 
	        $country=filter_var($_POST['country'],FILTER_SANITIZE_STRING); 
            $statusOrClinicStatus=filter_var($_POST['user_or_clinic_status'],FILTER_SANITIZE_NUMBER_INT);
            $lat=filter_var($_POST['lat'],FILTER_SANITIZE_NUMBER_FLOAT);
            $long=filter_var($_POST['long'],FILTER_SANITIZE_NUMBER_FLOAT);

	        if($type=="add")		  
	          add($email,$password,$userType,$username,$address,$phone,$country,$statusOrClinicStatus,$lat,$long,$con);
		    elseif($type=="change user type")
		      changeUserType($userType,$userId,$con);
		    elseif($type=="change status")
		      changeUserStatus($statusOrClinicStatus,$userId,$con);
		    else 
		      load($con);


		}
		else
			echo '{"status":0,"message":"you must came with post request"}';
		


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add user
function add($email,$password,$userType,$username,$address,$phone,$country,$ClinicStatus,$lat,$long,$con)
	{
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            try
            {

                 $checkState=$con->prepare("SELECT * FROM users where email=?");
				 $checkState->execute(array($email)); 

                if($checkState->rowCount()==0)
                {
            	//inserting user to database
            	$state=$con->prepare("INSERT INTO users(username,email,password,type,address,d_clinick_status,lat,longtude) VALUES (?,?,?,?,?,?,?,?)");
            	$state->execute(array($username,$email,$password,$userType,$address,$ClinicStatus,$lat,$long));          

                if(!empty($con->lastInsertId()))
	    		{

	    			$lastInsertedUserId=$con->lastInsertId();
                     
                    //inserting user country and phone  
	                $state3=$con->prepare("INSERT INTO countries(user_id,country_name) VALUES (?,?);
                          INSERT INTO phones(user_id,number) VALUES (?,?)");
	            	$state3->execute(array($lastInsertedUserId,$country,$lastInsertedUserId,$phone));
	            	$state3->closeCursor();

 		           load($con);

			    }
                else
                  echo '{"status":0,"message":"error while adding user"}';
                }
                else
                 echo '{"status":0,"message":"email already exist"}';

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
		}
        else
            echo '{"status":0,"message":"email not correct"}';

	}

function load($con){


			try
			{
				$state=$con->prepare("
					SELECT CONCAT( '[',

					 GROUP_CONCAT(JSON_OBJECT(
					 'user_id', user_id, 
					 'username', username, 
					 'email', email,
					 'type', type, 
					 'address', address, 
					 'created_at', date, 
					 'status', status,
					 'token',token,
					 'image_url',image_url,
					 'about_doctor',about_doctor,
					 'lat',lat,
					 'long',longtude,
					 'd_clinick_status',d_clinick_status,
					 'd_closing_reason',d_closing_reason,
					 'rating',(SELECT ifnull(AVG(rate),0) FROM ratings where user2_id=user_id),
                     'phones', (SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where user_id=users.user_id),
                     'countries', (SELECT CONCAT( '[\"',GROUP_CONCAT(country_name SEPARATOR '\",\"') , '\"]' ) FROM countries where user_id=users.user_id)
                     
					 ) )

					 
					 ,']') as data FROM users where type!='full_access'");
				$state->execute(array()); 
				$data = $state->fetchAll();
			     
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
			   
			}
			catch(PDOException $ex)
			{
	   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
	}


}

function changeUserStatus($status,$userId,$con)
{
        try
        {
        	$state=$con->prepare("UPDATE  users set status=? where user_id=?");
        	$state->execute(array($status,$userId));            
        	load($con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}

}


function changeUserType($userType,$userId,$con)
{
        try
        {
        	$state=$con->prepare("UPDATE  users set type=? where user_id=?");
        	$state->execute(array($userType,$userId));            
        	load($con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}

}