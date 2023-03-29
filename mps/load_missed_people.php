<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{

			        $user_id=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT);

			       $missedData=loadMissed($user_id,$con);
                   $foundData=loadFound($user_id,$con);
			       echo '{"status":1,"message":"loaded successfully","data":'.$missedData.',"found":'.$foundData.'}';
                  
				}
				else
			     	echo '{"status":0,"message":"you must came with post request","data":{}}';
				


	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database","data":{}}';
	}

//loading data 
function loadMissed($userId,$con)
{
			try
			{
				//getting flat data
				$state=$con->prepare("
					SELECT CONCAT( '[',

					  GROUP_CONCAT(JSON_OBJECT(

					 'id', id, 
					 'missed_type', missed_type, 
					 'name', name, 
					 'sex', sex, 
					 'missed_image', missed_image, 
					 'age', age,
					 'helthy_status', helthy_status,
					 'last_place',last_place, 
					 'missed_status', missed_status, 
					 'face_color', face_color, 
					 'hair_color', hair_color, 
					 'eye_color', eye_color,
					 'refuse_reason',refuse_reason,			
					 'created_at',created_at, 
                      'suggestions',(SELECT CONCAT( '[',

				 GROUP_CONCAT(
				 JSON_OBJECT('suggest_status',suggest_status,'date',date,'fount_id',fount_id,'f_user_id',f_user_id,'f_username',f_username,'f_user_image',f_user_image,'f_user_token',f_user_token,'f_missed_image',f_missed_image )),']')

                       from missed_suggestion_v where missed_id=missed_people_view.id
                      )



					 ) )

					 
					 ,']' ) as data

					 FROM missed_people_view WHERE user_id=? ");
				$state->execute(array($userId)); 
				$data = $state->fetchAll();
			    if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
			   
			}
			catch(PDOException $ex)
			{
	   echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
	}
	}


//loading  
function loadFound($userId,$con)
	{
				$stateee=$con->prepare("SELECT CONCAT( '[',


				 GROUP_CONCAT(
				 JSON_OBJECT( 

				 'date',date,
				 'user_id',user_id,
				   'username',username,
				 'user_image',user_image, 
				 'missed_type',missed_type,
				 'name',name,
				 'missed_image',missed_image,
				 'last_place',last_place,
				 'user_phones',(SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where user_id=missed_suggestion_v.user_id),
				 'f_user_id',f_user_id,
                 'f_username',f_username,
				 'f_user_image',f_user_image, 
				 'f_missed_type',f_missed_type,
				 'f_missed_image',f_missed_image,
				 'f_last_place',f_last_place,
                  'f_user_phones',(SELECT CONCAT( '[\"',GROUP_CONCAT(number SEPARATOR '\",\"') , '\"]' ) FROM phones where user_id=missed_suggestion_v.f_user_id)

				 ))

				  ,']' ) as data FROM missed_suggestion_v where (user_id=? or f_user_id=?) and suggest_status='identical'");
                $stateee->execute(array($userId,$userId)); 
				$data = $stateee->fetchAll();
              
              if(empty($data[0]['data']))
                return "[]";
			  else
			   return $data[0]['data'];
 
	}
	