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
				$userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);      
				$categoryId=filter_var($_POST['category_id'],FILTER_SANITIZE_NUMBER_INT);      
                $desc=filter_var($_POST['desc'],FILTER_SANITIZE_STRING); 
				$status=filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);      
				$imageName=$_POST['image_name'];
				$oldImageName=$_POST['old_image_name'];
				$imageFile=base64_decode($_POST['base64_image']);
                $price=filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_FLOAT);

                if($type=='add')
                 {
						$uploaded=file_put_contents("courses_images\\".$imageName,$imageFile);
						if($uploaded)
						   add($desc,$userId,$name,$categoryId,$url.'courses_images/'.$imageName,$price,$con);
						else
							Echo '{"status":0,"message" :"error while uploading image"}'; 

							  }
				elseif($type=="update course name")
				{
					    update($userId,'name',$name,$id,$con);
					}
					elseif($type=="update course status")
				{
					    update($userId,'status',$status,$id,$con);
					}
					elseif($type=="update course price")
				{
					    update($userId,'price',$price,$id,$con);
					}
				elseif($type=="delete")
				{
						Unlink("courses_images\\".$imageName);
						delete($userId,$id,$con);
					}
				elseif($type=="update course image")
				{
						Unlink("courses_images\\".$oldImageName);

						if(file_put_contents("courses_images\\".$imageName,$imageFile))
								update($userId,'image',$url.'courses_images/'.$imageName,$id,$con);
						else
								Echo '{"status":0,"message" :"error while uploading image"}';
						}
				else
				       load($userId,$con);
		}
		else
			echo '{"status":0,"message":"you must came with post request"}';

	}
	catch(PDOException $ex){
	            echo '{"status":0,"message":"failed to open database"}';}


//add book
	function add($desc,$userId,$name,$categoryId,$imageUrl,$price,$con){
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO course(name,description,image,user_id,category_id,price) values (?,?,?,?,?,?)");
			                	$state->execute(array($name,$desc,$imageUrl,$userId,$categoryId,$price));

			                	mkdir("videos\\".$con->lastInsertId());            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

//update book
	function update($userId,$field,$value,$courseId,$con){
	
			                try
			                {
			                	$state=$con->prepare("UPDATE course  set ".$field."=? where id=?");
			                	$state->execute(array($value,$courseId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

//delete book
	function delete($userId,$courseId,$con){
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  course where id=?");
			                	$state->execute(array($courseId));    

			                	$dirPath="videos\\".$courseId;
			                	foreach (glob($dirPath.'/*') as $file) {
			                		rmdir($file);
			                	}

			                	rmdir($dirPath);        
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

	function load($userId,$con)
	{
				$state=$con->prepare("

					SELECT  ifnull(CONCAT( '[',

				 GROUP_CONCAT(JSON_OBJECT(
					'id',id,
					'name',name,
					'desc',description,
					'image_url',image,
					'date',datee,
					'status',status,
					'category',category,
					'like_count',like_count,
					'black_count',black_count,
                    'rate',rate,
					'user_count_rating',user_count_rating,
                    'user_count',user_count,
                     'video_count',  video_count,
                      'discount_id', discount_id, 
					 'dis_percentage', discount_percentage, 
					 'price_after_dis', price_after_dis, 
					  'dis_status', dis_status, 
                    'dis_end_in',end_in,
					  'main_admin_status',main_admin_status,
					  'main_admin_stop_reason',main_admin_stop_reason,
					   'user_id', user_id, 
			         'user_image',image_url,
			         'username',username,
			         'user_token',token,
			         'price',price
						))
					 
					 ,']' ),'[]') as data  FROM course_view where user_id=?");

	$state2=$con->prepare("SELECT ifnull(CONCAT( '[',

				 GROUP_CONCAT(JSON_OBJECT(
					'id',id,
					'name',name,
					'desc',description
			
						))
					 
					 ,']' ),'[]') as data2  FROM category where type='course' and status=1");


				$state3=$con->prepare("SELECT ifnull(CONCAT( '[', GROUP_CONCAT(JSON_OBJECT( 'id',id, 'name',name, 'discount_percentage',discount_percentage,'description',description, 'end_in',end_in, 'created_at',created_at, 'modified_at',modified_at, 'deleted_at',deleted_at, 'status',status )) ,']' ),'[]')  as data3 FROM discount where admin_id=?");
               

                $state->execute(array($userId)); 
				$data = $state->fetchAll();
                $state2->execute(array()); 
				$data2 = $state2->fetchAll();
                $state3->execute(array($userId)); 
				$data3 = $state3->fetchAll();
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].',"courses_categories":'.$data2[0]['data2'].',"discounts":'.$data3[0]['data3'].'}';
		}