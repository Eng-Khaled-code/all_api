<?php
	            
	            $url="http://192.168.43.109/all_api/courses/";

	try
	{
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
	{
				$type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
				$id=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);      
                $name=filter_var($_POST['name'],FILTER_SANITIZE_STRING);
                $desc=filter_var($_POST['desc'],FILTER_SANITIZE_STRING); 
                $secId=filter_var($_POST['sec_id'],FILTER_SANITIZE_NUMBER_INT);      
                $courseId=filter_var($_POST['course_id'],FILTER_SANITIZE_NUMBER_INT);      
	            $videoFileName=$_POST['video_file_name'];
				$oldvideoFileName=$_POST['old_video_file_name'];
				$videoFile=base64_decode($_POST['base64video']);


			        if($type=='add')
			        {

		                        $fileePath="videos\\" . $courseId . "\\" . $secId . "\\" . $videoFileName;
		                        if(file_put_contents($fileePath,$videoFile))
		                        {
					        		$videoUrl=$url."videos/".$courseId."/".$secId."/".$videoFileName;
								    add($desc,$secId,$name,$videoUrl,$con);
						         }
						         else
						         {
						             Echo '{"status":0,"message" :"error while uploading video"}';	
						         }				 
			        }
			        elseif($type=="update"){
	                 
	                          if($oldvideoFileName != "no")
	                          { 

	                                    $filePath="videos\\".$courseId."\\".$secId."\\";

				        		        Unlink($filePath.$oldvideoFileName);

							        	if(file_put_contents($filePath.$videoFileName,$videoFile))
							        	{
							        		 $videoUrl=$url."videos/".$courseId."/".$secId."/".$videoFileName;
				                             update($id,$desc,$secId,$name,$videoUrl,$con);

								          } 
								          else
								          Echo '{"status":0,"message" :"error while uploading image"}';

					          }
					          else if($oldvideoFileName=="no")
					          {
					          	         $videoUrl=$url."videos/".$courseId."/".$secId."/".$videoFileName;
					          	         update($id,$desc,$secId,$name,$videoUrl,$con);
					          }
					        
						  
			        }
                    else if($type=="delete"){

                        $filePath="videos\\".$courseId."\\".$secId."\\".$videoFileName;
                    	Unlink($filePath);
                    	delete($id,$secId,$con);
                    }
                    else
                    {
        	           load($secId,$con);
                    }

			}
				else
				echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}


	function add($desc,$secId,$name,$videoUrl,$con)
	{
	



			                try
			                {
			                	$state=$con->prepare("INSERT INTO section_videos(name,description,sec_id,url) values (?,?,?,?)");
			                	$state->execute(array($name,$desc,$secId,$videoUrl));            
			                	load($secId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

//update discount
	function update($videoId,$desc,$secId,$name,$videoUrl,$con)
	{
	
        try
        {
        	$state=$con->prepare("UPDATE  section_videos set name=?,description=?,url=? where video_id=?");
        	$state->execute(array($name,$desc,$videoUrl,$videoId));            
        	load($secId,$con);
             
        }
        catch(PDOException $ex)
	    {
			   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
		}
					
	}

//delete discount
	function delete($id,$secId,$con)
	{
	
			                try
			                {
			                	$statek=$con->prepare("DELETE FROM  section_videos where video_id=?");
			                	$statek->execute(array($id));            
			                	load($secId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					
	}

	function load($secId,$con)
	{
				$state=$con->prepare("SELECT ifnull(CONCAT( '[', GROUP_CONCAT(JSON_OBJECT(
				 'video_id',video_id,
				  'name',name, 
				  'created_at',date,
				  'sec_id',sec_id,
				  'description',description,
				  'video_url',url

				)) ,']' ),'[]') as data FROM section_videos where sec_id=?");
                $state->execute(array($secId)); 
				$data = $state->fetchAll();
              
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
	}



