<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
                    //add or update
                    $addOrUpdate=filter_var($_POST['addOrUpdate'],FILTER_SANITIZE_STRING); 
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        //بيع او ايجار
			        $operation=filter_var($_POST['operation'],FILTER_SANITIZE_STRING); 

                    $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $more_details=filter_var($_POST['more_details'],FILTER_SANITIZE_STRING); 
			        $rouf_num=filter_var($_POST['rouf_num'],FILTER_SANITIZE_NUMBER_INT);      
			        $size=filter_var($_POST['size'],FILTER_SANITIZE_NUMBER_FLOAT);
			        $metre_price=filter_var($_POST['metre_price'],FILTER_SANITIZE_NUMBER_FLOAT); 
 			        $address=filter_var($_POST['address'],FILTER_SANITIZE_STRING);      
                    $opeId=filter_var($_POST['ope_id'],FILTER_SANITIZE_NUMBER_INT); 

			        if($addOrUpdate=='add')
					  add($type,$operation,$adminId,$more_details,$rouf_num,$size,$metre_price,$address,$con);
			        elseif($addOrUpdate=="update")
					  update($opeId,$type,$operation,$adminId,$more_details,$rouf_num,$size,$metre_price,$address,$con);
                    else 
                    	delete($opeId,$con);

				}
				else
					echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}



    //add
	function add($type,$operation,$adminId,$more_details,$rouf_num,$size,$metre_price,$address,$con)
	{
			                try
			                {

			                	    $state=$con->prepare("INSERT INTO place(address,type,size,metre_price,
			                	    	operation,its_rouf_num,more_details,admin_id,status) VALUES (?,?,?,?,?,?,?,?,?)");
			                	    $state->execute(array($address,$type,$size,$metre_price,$operation,$rouf_num,$more_details,$adminId,0));       
	                            
	                           echo '{"status":1,"message":"added successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
	}


    //update 
	function update($opeId,$type,$operation,$adminId,$more_details,$rouf_num,$size,$metre_price,$address,$con)
	{
			                try
			                {

                               
			                	    $state=$con->prepare("UPDATE place SET address=?,type=?,size=?,
			                	    	metre_price=?,operation=?,its_rouf_num=?,more_details=? WHERE admin_id=? and place_id=?");
			                	    $state->execute(array($address,$type,$size,$metre_price,$operation,$rouf_num,$more_details,$adminId,$opeId));       
			             	            echo '{"status":1,"message":"updated successfully"}';

	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
						

	}


	function delete($opeId,$con){

			                try
			                {

			                	    $state=$con->prepare("DELETE FROM place WHERE place_id=?");
			                	    $state->execute(array($opeId));       
			             	            echo '{"status":1,"message":"deleted successfully"}';

	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
						


	}


