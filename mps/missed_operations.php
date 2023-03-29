<?php

 try
 {
    
    include '../config/config.php';

    if($_SERVER['REQUEST_METHOD']=='POST')
    {
                    //add or update or delete
           $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING);  
           $id=filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT); 
           $missedOrFound=filter_var($_POST['missed_or_found'],FILTER_SANITIZE_STRING);
           $missedOrFoundEnglish=filter_var($_POST['missed_or_found_english'],FILTER_SANITIZE_STRING); 
                    $status=filter_var($_POST['missed_status'],FILTER_SANITIZE_STRING); 
                    $userId=filter_var($_POST['user_id'],FILTER_SANITIZE_NUMBER_INT); 
           $name=filter_var($_POST['name'],FILTER_SANITIZE_STRING); 
           $sex=filter_var($_POST['sex'],FILTER_SANITIZE_STRING); 
           $helthyStatus=filter_var($_POST['helthy_status'],FILTER_SANITIZE_STRING);
           $age=filter_var($_POST['age'],FILTER_SANITIZE_NUMBER_INT); 
            $lastPlace=filter_var($_POST['last_place'],FILTER_SANITIZE_STRING);
            $faceColor=filter_var($_POST['face_color'],FILTER_SANITIZE_STRING);      
            $hairColor=filter_var($_POST['hair_color'],FILTER_SANITIZE_STRING);      
            $eyeColor=filter_var($_POST['eye_color'],FILTER_SANITIZE_STRING);      
            $statusAfterUpdate=filter_var($_POST['status_after_update'],FILTER_SANITIZE_STRING);
                    $image_url=$_POST['image_name'];
           $old_image_name=$_POST['old_image_name'];
           $imageFile=base64_decode($_POST['base64']);
                    
                    $middleUrl="";
                    $middlePath="";
                    
                    if($missedOrFoundEnglish=="missed" and $status=="waiting")
                    {
                     $middleUrl="missed_images\\waiting\\";
                     $middlePath="missed_images/waiting/";
                    }
                    else if($missedOrFoundEnglish=="missed" and $status=="accept")
                    {
                     $middleUrl="missed_images\\accepted\\";
                     $middlePath="missed_images/accepted/";
                    }
                    else if($missedOrFoundEnglish=="missed" and $status=="refuse")
                    {
                     $middleUrl="missed_images\\refused\\";
                     $middlePath="missed_images/refused/";
                    }
                    else if($missedOrFoundEnglish=="found" and $status=="waiting")
                    {
                     $middleUrl="found_images\\waiting\\";
                     $middlePath="found_images/waiting/";
                    }
                    else if($missedOrFoundEnglish=="found" and $status=="accept")
                    {
                     $middleUrl="found_images\\accepted\\";
                     $middlePath="found_images/accepted/";
                    }
                    else if($missedOrFoundEnglish=="found" and $status=="refuse")
                    {
                     $middleUrl="found_images\\refused\\";
                     $middlePath="found_images/refused/";
                    }

           if($type=='add'){

            if(file_put_contents("images\\".$middleUrl.$image_url,$imageFile)){
               add($missedOrFound,$userId,$name,$sex,$helthyStatus,$age,$lastPlace,$middlePath.$image_url,$faceColor,$hairColor,$eyeColor,$con);
             }
             else
              Echo '{"status":0,"message" :"error while uploading image"}';

      
           }
           elseif($type=="update"){
                 
                          if($old_image_name != "no"){ 
             Unlink("images\\".$middleUrl.$old_image_name);

              if(file_put_contents("images\\".$middleUrl.$image_url,$imageFile))
              {
                             update($id,$name,$sex,$helthyStatus,$age,$lastPlace,$middlePath.$image_url,$faceColor,$hairColor,$eyeColor,$con);
                              }              else
              Echo '{"status":0,"message" :"error while uploading image"}';
                         }
                          else
                             {
                              update($id,$statusAfterUpdate,$name,$sex,$helthyStatus,$age,$lastPlace,$image_url,$faceColor,$hairColor,$eyeColor,$con);
                             }
             
       
           }

                    else {

                     Unlink("images\\".$middleUrl.$image_url);
                     delete($id,$con);
                    }

    }
    else
     echo '{"status":0,"message":"you must came with post request","data":{}}';
    


 }
 catch(PDOException $ex)
 {
             echo '{"status":0,"message":"failed to open database","data":{}}';
 }



    //add
 function add($missedOrFound,$userId,$name,$sex,$helthyStatus,$age,$lastPlace,$image_url,$faceColor,$hairColor,$eyeColor,$con)     
 {     
  try
                   {

                        $state=$con->prepare("INSERT INTO missed_people(type,user_id,name,sex,
                         helthy_status,age,last_place,image_url,face_color,hair_color,eye_color) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                        $state->execute(array($missedOrFound,$userId,$name,$sex,$helthyStatus,$age,$lastPlace,'http://192.168.43.109/all_api/mps/images/'.$image_url,$faceColor,$hairColor,$eyeColor));       
                             
                            echo '{"status":1,"message":"added successfully"}';

             }
             catch(PDOException $ex)
          {
           echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
       }
 }


    //update 
 function update($id,$statusAfterUpdate,$name,$sex,$helthyStatus,$age,$lastPlace,$image_url,$faceColor,$hairColor,$eyeColor,$con){
                   try
                   {

                               
                        $state=$con->prepare("UPDATE missed_people SET status =?, name=?,sex=?,helthy_status=?,
                         age=?,last_place=?,image_url=?,face_color=?,hair_color=?,eye_color=? WHERE id=?");
                        $state->execute(array($statusAfterUpdate,$name,$sex,$helthyStatus,$age,$lastPlace,'http://192.168.43.109/all_api/mps/images/'.$image_url,$faceColor,$hairColor,$eyeColor,$id));      
                             echo '{"status":1,"message":"updated successfully"}';

                              
             }
             catch(PDOException $ex)
          {
           echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
       }
      

 }


 function delete($id,$con){

                   try
                   {

                        $state=$con->prepare("DELETE FROM missed_people WHERE id=?");
                        $state->execute(array($id));       
                             echo '{"status":1,"message":"deleted successfully"}';

                              
             }
             catch(PDOException $ex)
          {
           echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
       }
     

 }


