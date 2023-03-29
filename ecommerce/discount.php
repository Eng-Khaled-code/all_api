<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $disId=filter_var($_POST['discount_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $name=filter_var($_POST['name'],FILTER_SANITIZE_STRING); 
			        $desc=filter_var($_POST['desc'],FILTER_SANITIZE_STRING); 
			        $endIn=filter_var($_POST['end_in'],FILTER_SANITIZE_STRING); 
			        $percentage=filter_var($_POST['percentage'],FILTER_SANITIZE_STRING);      
			      	$userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);      
			        $status=filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT); 

			      if($type=="add")
			       add($name,$desc,$percentage,$endIn,$userId,$con);
			      elseif($type=="update")
			       update($disId,$name,$desc,$percentage,$endIn,$userId,$con);
			      else if($type=="delete")
			       delete($disId,$userId,$con);
			      else if($type=="change status")
			       changeStatus($disId,$userId,$status,$con);
			      else
			      	load($userId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add discount
	function add($name,$desc,$percentage,$endIn,$userId,$con)
	{
	



			                try
			                {
			                	$state=$con->prepare("INSERT INTO discount(name,description,discount_percentage,end_in,admin_id) values (?,?,?,?,?)");
			                	$state->execute(array($name,$desc,$percentage,$endIn,$userId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update discount
	function update($disId,$name,$desc,$percentage,$endIn,$userId,$con)
	{
	
        try
        {
        	$state=$con->prepare("UPDATE  discount set name=? , description=?, discount_percentage=?,end_in=?,modified_at=CURRENT_TIMESTAMP where id=?");
        	$state->execute(array($name,$desc,$percentage,$endIn,$disId));            
        	load($userId,$con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}

//change status
	function changeStatus($disId,$userId,$status,$con)
	{
	
            try
            {
            	$state;
            	if($status==0)
            	  $state=$con->prepare("UPDATE  discount set status=?,deleted_at=CURRENT_TIMESTAMP,modified_at=CURRENT_TIMESTAMP where id=?");
            	else
            	  $state=$con->prepare("UPDATE  discount set status=?,modified_at=CURRENT_TIMESTAMP where id=?");

            	$state->execute(array($status,$disId));            
            	load($userId,$con);
                 
	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
	
	}
//delete discount
	function delete($disId,$userId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  discount where id=?");
			                	$statek->execute(array($disId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($userId,$con)
	{
				$state=$con->prepare("SELECT CONCAT( '[', GROUP_CONCAT(JSON_OBJECT( 'id',id, 'name',name, 'discount_percentage',discount_percentage,'description',description, 'end_in',end_in, 'created_at',created_at, 'modified_at',modified_at, 'deleted_at',deleted_at, 'status',status )) ,']' ) as data FROM discount where admin_id=?");
                $state->execute(array($userId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}