<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
                    //add or update
                        
                    $opeId=filter_var($_POST['ope_id'],FILTER_SANITIZE_NUMBER_INT); 
                    $buyerId=filter_var($_POST['buyer'],FILTER_SANITIZE_NUMBER_INT); 
                    	update($opeId,$buyerId,$con);

				}
				else
					echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

function update($opeId,$buyerId,$con){

	  try
        {

        	    $state=$con->prepare("UPDATE discus SET status='buy' WHERE place_id=? and user_id=?;
                    DELETE from discus WHERE place_id=? and status='discuss'");
        	    $state->execute(array($opeId,$buyerId,$opeId));       
     	            echo '{"status":1,"message":"successfully"}';

        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
}
