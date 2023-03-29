<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $bookId=filter_var($_POST['book_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $docIdAndBookType=filter_var($_POST['doc_id'],FILTER_SANITIZE_STRING); 
			        $userIdOrNotes=filter_var($_POST['user_id'],FILTER_SANITIZE_STRING); 
			        $patientNameOrBookStatus=filter_var($_POST['patient_name'],FILTER_SANITIZE_STRING);
			        $painDescOrDate=filter_var($_POST['pain_desc'],FILTER_SANITIZE_STRING); 

			      if($type=="add")
			       add($docIdAndBookType,$userIdOrNotes,$patientNameOrBookStatus,$painDescOrDate,$con);
			      else if($type=="delete")
			      	delete($bookId,$con);
			      else if($type=="update")
			      	update($bookId,$patientNameOrBookStatus,$painDescOrDate,$con);
			      else if($type=="add from clinick")
			      	addFromClinick($docIdAndBookType,$patientNameOrBookStatus,$userIdOrNotes,$painDescOrDate,$con);
			      else
			      	changeStatus($bookId,$patientNameOrBookStatus,$painDescOrDate,$userIdOrNotes,$docIdAndBookType,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add 
	function add($docId,$userId,$patientName,$painDesc,$con)
	{
	



	    try
	    {
	    	$state=$con->prepare("INSERT INTO doctors_booking(doc_id,user_id,patient_name,pain_desc) values (?,?,?,?)");
	    	$state->execute(array($docId,$userId,$patientName,$painDesc)); 

	           echo '{"status":1,"message":"successfully"}';
	         
	    }
	    catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}


	function delete($bookId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  doctors_booking where book_id=?");
			                	$statek->execute(array($bookId));            
	                             echo '{"status":1,"message":"successfully"}';

					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function update($bookId,$patientName,$painDesc,$con){

		    try
            {
            	$state=$con->prepare("UPDATE  doctors_booking set patient_name=? ,pain_desc=? where book_id=?");
            	$state->execute(array($patientName,$painDesc,$bookId));            
                echo '{"status":1,"message":"successfully"}';

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
	}


	function changeStatus($bookId,$status,$date,$notes,$bookType,$con){

		    try
            {
            	$state=$con->prepare("UPDATE  doctors_booking set type=? ,booking_status=?,notes=?,result_date=CURRENT_TIMESTAMP,booking_final_date=(SELECT CASE WHEN ?='ACCEPTED' THEN ? ELSE booking_final_date END),
            		num_in_queue=(
            		SELECT CASE WHEN ?='ACCEPTED' THEN (select ifnull(max(db.num_in_queue),0)+1 from doctors_booking db where db.booking_final_date=?) ELSE num_in_queue END) where book_id=?");
            	$state->execute(array($bookType,$status,$notes,$status,$date,$status,$date,$bookId));            
                echo '{"status":1,"message":"successfully"}';

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
	}


//add 
	function addFromClinick($docId,$patientName,$date,$painDesc,$con)
	{
	



	    try
	    {
	    	$state=$con->prepare("INSERT INTO doctors_booking(doc_id,patient_name,pain_desc,booking_status,booking_final_date,num_in_queue) values (?,?,?,'ACCEPTED',?,(select ifnull(max(db.num_in_queue),0)+1 from doctors_booking db where db.booking_final_date=?))");
	    	$state->execute(array($docId,$patientName,$painDesc,$date,$date)); 

	           echo '{"status":1,"message":"successfully"}';
	         
	    }
	    catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}
