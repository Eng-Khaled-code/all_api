<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $secId=filter_var($_POST['sec_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $name=filter_var($_POST['name'],FILTER_SANITIZE_STRING); 
			        $courseId=filter_var($_POST['course_id'],FILTER_SANITIZE_NUMBER_INT); 

			      if($type=="add")
			       add($name,$courseId,$con);
			      elseif($type=="update")
			       update($secId,$name,$courseId,$con);
			      else if($type=="delete")
			       delete($secId,$courseId,$con);
			      else
			      	load($courseId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add discount
	function add($name,$courseId,$con)
	{
	



			                try
			                {
			                	$state=$con->prepare("INSERT INTO course_section(name,course_id) values (?,?)");
			                	$state->execute(array($name,$courseId));  
			                	mkdir("videos\\".$courseId."\\".$con->lastInsertId());            
          
			                	load($courseId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update discount
	function update($secId,$name,$courseId,$con)
	{
	
        try
        {
        	$state=$con->prepare("UPDATE  course_section set name=? where id=?");
        	$state->execute(array($name,$secId));            
        	load($courseId,$con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}

//delete discount
	function delete($secId,$courseId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  course_section where id=?");
			                	$statek->execute(array($secId));    
			                    rmdir("videos\\".$courseId."\\".$secId);        
        
			                	load($courseId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($courseId,$con)
	{
				$state=$con->prepare("SELECT ifnull(CONCAT( '[', GROUP_CONCAT(JSON_OBJECT(
				 'id',id,
				  'name',name, 
				  'created_at',created_at,
				  'course_id',course_id,
				  'sec_videos',(select count(*) from section_videos where sec_id=course_section.id)

				)) ,']' ),'[]') as data FROM course_section where course_id=?");
                $state->execute(array($courseId)); 
				$data = $state->fetchAll();
              
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}