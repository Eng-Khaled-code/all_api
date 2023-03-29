<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $queId=filter_var($_POST['que_id'],FILTER_SANITIZE_NUMBER_INT); 
			        $question=filter_var($_POST['question'],FILTER_SANITIZE_STRING); 
			        $res1=filter_var($_POST['res1'],FILTER_SANITIZE_STRING); 
			        $res2=filter_var($_POST['res2'],FILTER_SANITIZE_STRING); 
			        $res3=filter_var($_POST['res3'],FILTER_SANITIZE_STRING); 
			        $res4=filter_var($_POST['res4'],FILTER_SANITIZE_STRING); 
			        $trueIndex=filter_var($_POST['true_index'],FILTER_SANITIZE_NUMBER_INT); 
			        $videoId=filter_var($_POST['video_id'],FILTER_SANITIZE_NUMBER_INT); 

			      if($type=="add")
			       add($question,$res1,$res2,$res3,$res4,$trueIndex,$videoId,$con);
			      elseif($type=="update")
			       update($queId,$question,$res1,$res2,$res3,$res4,$trueIndex,$videoId,$con);
			      else if($type=="delete")
			       delete($queId,$videoId,$con);
			      else
			      	load($videoId,$con);
				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}


//add discount
	function add($question,$res1,$res2,$res3,$res4,$trueIndex,$videoId,$con)
	{
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO video_questions(question,res1,res2,res3,res4,true_index,video_id) values (?,?,?,?,?,?,?)");
			                	$state->execute(array($question,$res1,$res2,$res3,$res4,$trueIndex,$videoId));  
          
			                	load($videoId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update discount
	function update($queId,$question,$res1,$res2,$res3,$res4,$trueIndex,$videoId,$con)
	{
	
        try
        {
        	$state=$con->prepare("UPDATE  video_questions set question=?, res1=?,res2=?,res3=?,res4=?,true_index=? where que_id=?");
        	$state->execute(array($question,$res1,$res2,$res3,$res4,$trueIndex,$queId));            
        	load($videoId,$con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}

//delete discount
	function delete($queId,$videoId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  video_questions where que_id=?");
			                	$statek->execute(array($queId));    
        
			                	load($videoId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($videoId,$con)
	{
				$state=$con->prepare("SELECT ifnull(CONCAT( '[', GROUP_CONCAT(JSON_OBJECT(
				 'que_id',que_id,
				  'question',question, 
				  'res1',res1,
				  'res2',res2,
                  'res3',res3,
				  'res4',res4,
                  'true_index',true_index,
				  'video_id',video_id
				)) ,']' ),'[]') as data FROM video_questions where video_id=?");
                $state->execute(array($videoId)); 
				$data = $state->fetchAll();
              
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}