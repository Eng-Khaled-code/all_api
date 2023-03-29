<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $id1=filter_var($_POST['id1'],FILTER_SANITIZE_NUMBER_INT); 
			        $id2=filter_var($_POST['id2'],FILTER_SANITIZE_NUMBER_INT); 

			      if($type=="suggest true")
			        setSuggestionTrue($id1,$id2,$con);
			      
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

function setSuggestionTrue($id1,$id2,$con)
{


		    try
            {
            	$state=$con->prepare("UPDATE  missed_suggestion set suggest_status=? where missed_id=? and fount_id=?;
                     Delete from missed_suggestion where missed_id=? and fount_id !=?
                
            		");
            	$state->execute(array('identical',$id1,$id2,$id1,$id2));            
                echo '{"status":1,"message":"successfully"}';

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
}
