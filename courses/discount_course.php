<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $disId=filter_var($_POST['discount_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $courseId=filter_var($_POST['course_id'],FILTER_SANITIZE_NUMBER_INT); 
			        
			       operations($disId,$type,$courseId,$con);
			      
			      
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				
	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

	function operations($disId,$type,$courseId,$con)
	{

		 try
			                {
			                	if($type=="add"){

			                	$state=$con->prepare("UPDATE  course set discount_id=? where id=?");
			                	$state->execute(array($disId,$courseId));            
	                             }
	                             else if($type=="update"){

			                	$state=$con->prepare("UPDATE  course set discount_id=? where id=?");
			                	$state->execute(array($disId,$courseId));            
	                             }
	                             else{
			                	$state=$con->prepare("UPDATE  course set discount_id=NULL where id=?");
			                	$state->execute(array($courseId));            
	                             
	                             }
                              echo '{"status":1,"message":"successfully"}'; 

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}
