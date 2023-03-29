<?php

	try
	{
				
				include '../config/config.php';

				if($_SERVER['REQUEST_METHOD']=='POST')
				{
			        $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
			        $id=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
			        $adminId=filter_var($_POST['admin_id'],FILTER_SANITIZE_NUMBER_INT); 
              $adminToken=filter_var($_POST['admin_token'],FILTER_SANITIZE_STRING);  
              $refuseReason=filter_var($_POST['refuse_reason'],FILTER_SANITIZE_STRING); 
              $missedType=filter_var($_POST['missed_type'],FILTER_SANITIZE_STRING); 
              $imageName=filter_var($_POST['image_name'],FILTER_SANITIZE_STRING);
              $status=filter_var($_POST['status'],FILTER_SANITIZE_STRING);

              if($type=="load")
                load($adminId,$con);
              else if($type=="add suggestion")
                addSuggestion($id,$imageName);
              else
              { 

                $movedFrom="";
                $moveTo="";
                $newImageUrl="";

                if($missedType=="missed" and $type=="accept"){
                  $movedFrom="images\missed_images\waiting\\".$imageName;
                  $moveTo="images\missed_images\accepted\\".$imageName;
                  $newImageUrl="missed_images/accepted/".$imageName;

                }
                else if($missedType=="missed" and $type=="refuse"){
                  $movedFrom="images\missed_images\waiting\\".$imageName;
                  $moveTo="images\missed_images\\refused\\".$imageName;
                  $newImageUrl="missed_images/refused/".$imageName;
                }
                else if($missedType=="found" and $type=="accept"){
                  $movedFrom="images\\found_images\waiting\\".$imageName;
                  $moveTo="images\\found_images\accepted\\".$imageName;
                  $newImageUrl="found_images/accepted/".$imageName;

                }
                else if($missedType=="found" and $type=="refuse"){
                  $movedFrom="images\\found_images\\waiting\\".$imageName;
                  $moveTo="images\\found_images\\refused\\".$imageName;
                  $newImageUrl="found_images/refused/".$imageName;

                }
     

                        if(rename($movedFrom,$moveTo) )
        			             acceptOrRefuse($id,$adminId,$status,$newImageUrl,$refuseReason,$adminToken,$con);
                        else
                             echo '{"status":0,"message":"something went wrong"}';

    			    }


				}
				else
			     	echo '{"status":0,"message":"you must came with post request"}';
				

	}
	catch(PDOException $ex)
	{
	            echo '{"status":0,"message":"failed to open database"}';
	}

function acceptOrRefuse($id,$adminId,$status,$newImageUrl,$refuseReason,$adminToken,$con)
{


		    try
            {
            	$state=$con->prepare("UPDATE  missed_people set status=? ,image_url=?,admin_id=?,refuse_reason=? where id=?;");
            	$state->execute(array($status,'http://192.168.43.109/all_api/mps/images/'.$newImageUrl,$adminId,$refuseReason,$id));
                      load($adminId,$con);
                //send notify                

	        }
	        catch(PDOException $ex)
		    {
				   echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
			}
}

function load($adminId,$con)
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
                     'user_id',user_id,
                     'username',username,
                     'user_image',user_image,
                     'user_token',user_token ,
                      'suggestions',(SELECT CONCAT( '[',

                 GROUP_CONCAT(
                 JSON_OBJECT(
                     'suggest_status',suggest_status,'date',date,'fount_id',fount_id,'f_user_id',f_user_id,'f_username',f_username,'f_user_image',f_user_image,'f_user_token',f_user_token,'f_missed_image',f_missed_image)),']')

                       from missed_suggestion_v where missed_id=missed_people_view.id
                      )

                     ) )

                     
                     ,']' ) as data

                     FROM missed_people_view WHERE admin_id is null or admin_id=?");
                $state->execute(array($adminId)); 
                $data = $state->fetchAll();
                 if(empty($data[0]['data']))
                echo '{"status":1,"message":"successfully","data":[]}';
              else
               echo '{"status":1,"message":"successfully","data":'.$data[0]['data'].'}';
 
               
            }
            catch(PDOException $ex)
            {
       echo '{"status":0,"message":"' . $ex->getMessage() . '","data":{}}';
            }

}



function addSuggestion($id1,$imageName){

       try
        {
            $state=$con->prepare("SELECT max(id) as id  from missed_people where image_url=?");
            $state->execute(array('http://192.168.43.109/all_api/mps/images/found_images/accepted/'.$imageName));      
           
            if($state->rowCount()>0)
            {
                $row=$state->fetch();

                $state3=$con->prepare("SELECT max(missed_id) from missed_suggestion where missed_id=? and fount_id =?");
                $state3->execute(array($id1,$row['id']));      
           
            if($state3->rowCount()==0)
            {
                $state2=$con->prepare("INSERT into missed_suggestion(missed_id,fount_id) values(?,?)");
                $state2->execute(array($id1,$row['id']));            
                load($adminId,$con);
               //send notification
               
               }
              else  echo '{"status":0,"message":"already suggested"}';

            }                 
            else
               echo '{"status":0,"message":"error"}';


        }
        catch(PDOException $ex)
        {
               echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
        }

}
