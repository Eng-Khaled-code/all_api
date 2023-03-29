<?php
	$url="http://192.168.43.109/all_api/read_books/";

	try
	{
				
		include '../config/config.php';

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
				$type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
				$name=filter_var($_POST['book_name'],FILTER_SANITIZE_STRING); 
				$bookId=filter_var($_POST['book_id'],FILTER_SANITIZE_NUMBER_INT);      
				$userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);      

				$imageName=$_POST['image_name'];
				$oldImageName=$_POST['od_image_name'];
				$imageFile=base64_decode($_POST['base64_image']);
							
				$bookNameEnglish=$_POST['book_name_english'];
				$oldBookNameEnglish=$_POST['old_book_name_english'];
				$bookFile=base64_decode($_POST['base64_file']);
						
                if($type=='add'){
						$uploaded=
						file_put_contents("books\\".$bookNameEnglish,$bookFile)&&
						file_put_contents("images\\".$imageName,$imageFile);
						if($uploaded)
						   add($userId,$name,$url.'books/'.$bookNameEnglish,$url.'images/'.$imageName,$con);
						else
							Echo '{"status":0,"message" :"error while uploading image"}';   }
				elseif($type=="update")
					    update($userId,'name',$name,$bookId,$con);
				elseif($type=="delete"){
						Unlink("images\\".$imageName);
						Unlink("books\\".$bookNameEnglish);
						delete($bookId,$con);}
				elseif($type=="update book image"){
						Unlink("images\\".$oldImageName);

						if(file_put_contents("images\\".$imageName,$imageFile))
								update($userId,'image_url',$url.'images/'.$imageName,$bookId,$con);
						else
								Echo '{"status":0,"message" :"error while uploading image"}';}
				elseif($type=="update book file"){
						Unlink("books\\".$oldBookNameEnglish);

						if(file_put_contents("books\\".$bookNameEnglish,$bookFile)==true)
								update($userId,'book_url',$url.'books/'.$bookNameEnglish,$bookId,$con);
						else
								Echo '{"status":0,"message" :"error while uploading image"}';}
				else
				       load($userId,$con);
		}
		else
			echo '{"status":0,"message":"you must came with post request"}';

	}
	catch(PDOException $ex){
	            echo '{"status":0,"message":"failed to open database"}';}


//add book
	function add($userId,$name,$bookUrl,$imageUrl,$con){
	
			                try
			                {
			                	$state=$con->prepare("INSERT INTO read_books(name,book_url,image_url) values (?,?,?)");
			                	$state->execute(array($name,$bookUrl,$imageUrl));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

//update book
	function update($userId,$field,$value,$bookId,$con){
	
			                try
			                {
			                	$state=$con->prepare("UPDATE read_books  set ".$field."=? where id=?");
			                	$state->execute(array($value,$bookId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

//delete book
	function delete($userId,$bookId,$con){
	
			                try
			                {
			                	$state=$con->prepare("DELETE FROM  read_books where id=?");
			                	$state->execute(array($bookId));            
			                	load($userId,$con);
	                             
					        }
					        catch(PDOException $ex)
						    {
								   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
							}
					}

//load books
	function load($userId,$con){
				$state=$con->prepare("SELECT CONCAT( '[',

				 GROUP_CONCAT(JSON_OBJECT(
					'id',id,
					'name',name,
					'book_url',book_url,
					'image_url',image_url,
					'like_count',(SELECT COUNT(*) from fav_books where book_id=id),
					'black_count',(SELECT COUNT(*) from black_books where book_id=id),
                    'is_fav',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM fav_books WHERE book_id=id and user_id=? ),
                    'is_black',(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM black_books WHERE book_id=id and user_id=? )
                                     
						))
					 
					 ,']' ) as data  FROM read_books ");
                $state->execute(array($userId,$userId)); 
				$data = $state->fetchAll();
              
              if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
			  else
			   echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';}