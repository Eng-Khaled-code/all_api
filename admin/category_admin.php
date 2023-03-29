<?php

 try
    {
    
  include '../config/config.php';

  if($_SERVER['REQUEST_METHOD']=='POST')
  {
      
      $type=filter_var($_POST['type'],FILTER_SANITIZE_STRING); 
      $categoryId=filter_var($_POST['category_id'],FILTER_SANITIZE_NUMBER_INT);
      $categoryName=filter_var($_POST['category'],FILTER_SANITIZE_STRING); 
      $categoryDescription=filter_var($_POST['category_description'],FILTER_SANITIZE_STRING); 
      $categoryStatus=filter_var($_POST['category_status'],FILTER_SANITIZE_NUMBER_INT);
      $categoryType=filter_var($_POST['category_type'],FILTER_SANITIZE_STRING); 

      if($type=="add")    
           add($categoryName,$categoryDescription,$categoryType,$con);
      elseif($type=="update")
        update($categoryId,$categoryName,$categoryDescription,$con);
      elseif($type=="change status")
        changeStatus($categoryId,$categoryStatus,$con);
      elseif($type=="load dasboard data")
        loadDashoardData($con);
      else 
        load($con,$categoryType,$type);
  }
  else
   echo '{"status":0,"message":"you must came with post request"}';
  


 }
 catch(PDOException $ex)
 {
             echo '{"status":0,"message":"failed to open database"}';
 }

function add($categoryName,$categoryDescription,$categoryType,$con){

                   try
                   {
                    $state=$con->prepare("INSERT INTO category(name,description,type) values (?,?,?)");
                    $state->execute(array($categoryName,$categoryDescription,$categoryType));            
                    load($con,$categoryType,'load full access');
                              
             }
             catch(PDOException $ex)
          {
           echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
       }
     
 }

//update phone
 function update($categoryId,$categoryName,$categoryDescription,$con)
 {
 
                   try
                   {
                    $state=$con->prepare("UPDATE  category set name=? ,description=?,modified_at=CURRENT_TIMESTAMP where id=?");
                    $state->execute(array($categoryName,$categoryDescription,$categoryId));            
                    load($con,'load full access');
                              
             }
             catch(PDOException $ex)
          {
           echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
       }
     
 }

function changeStatus($categoryId,$categoryStatus,$con)
{
        try
        {
         $state=$con->prepare("UPDATE  category set status=? ,changing_status_date=CURRENT_TIMESTAMP where id=?");
         $state->execute(array($categoryStatus,$categoryId));            
         load($con,'load full access');
             
        }
        catch(PDOException $ex)
     {
      echo '{"status":0,"message":"' . $ex->getMessage() . '"}';
  }

}


 function load($con,$categoryType,$type)
 {
    try
            {

             $query="";
             if($type=="load full access")
             {
              $query="SELECT CONCAT( '[',

      GROUP_CONCAT(JSON_OBJECT(
         'id',id,
         'name',name,
         'description',description,
                                    'created_at',created_at,
                                    'modified_at',modified_at,
                                    'status',status,
                                    'changing_status_date',changing_status_date,
                                    'item_count',(select count(*) from product where category_id=category.id),
                                     'courses_count',(select count(*) from course where  category_id= category.id)

         ))
      
      ,']' ) as data  FROM category where type=? ";
     }
      else
      {
$query="SELECT CONCAT( '[',

      GROUP_CONCAT(JSON_OBJECT(
         'id',id,
         'name',name,
         'description',description,
                                    'created_at',created_at,
                                    'modified_at',modified_at,
                                    'status',status,
                                    'changing_status_date',changing_status_date,
                                    'item_count',(select count(*) from product where category_id=category.id),
                                    'courses_count',(select count(*) from course where  category_id= category.id)

         ))
      
      ,']' ) as data  FROM category where status=1 and type=? ";
      }
             
             $state=$con->prepare($query);
                $state->execute(array($categoryType)); 
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

 function loadDashoardData($con)
 {
    try
      {

                        
         $miss='فقد';
         $found='إيجاد';
         $wait='انتظار';
         $accept='مقبول';
         $refuse='مرفوض';
         $query="SELECT JSON_OBJECT(
                  'places_count',(select count(*) from place),
                  'flat_count',(select count(*) from place where type='flat'),
                  'buy_flat_count',(select count(*) from discuss_view where place_type='flat' and status ='buy'),
                  'block_count',(select count(*) from place where type='block'),
                  'buy_block_count',(select count(*) from discuss_view where place_type='block' and status ='buy'),
                  'store_count',(select count(*) from place where type='local_store'),
                  'buy_store_count',(select count(*) from discuss_view where place_type='local_store' and status ='buy'),
                  'ground_count',(select count(*) from place where type='ground'),
                  'buy_ground_count',(select count(*) from discuss_view where place_type='ground' and status ='buy'),
                  'person_count',(select count(*) from missed_people),
                  'miss_count',(select count(*) from missed_people where type=?),
                  'found_count',(select count(*) from missed_people where type=?),
                  'wait_miss_count',(select count(*) from missed_people where type=?  and status=?),
                  'accept_miss_count',(select count(*) from missed_people where type=? and status=?),
                  'refuse_miss_count',(select count(*) from missed_people where type=? and status=?),
                  'wait_found_count',(select count(*) from missed_people where type=? and status=?),
                  'accept_found_count',(select count(*) from missed_people where type=? and status=?),
                  'refuse_found_count',(select count(*) from missed_people where type=? and status=?),
                  'final_found_count',(select count(*) from missed_suggestion where suggest_status='identical')
                  ) as data
            ";
      
             
             $state=$con->prepare($query);
             $state->execute(array($miss,$found,$miss,$wait,$miss,$accept,$miss,$refuse,$found,$wait,$found,$accept,$found,$refuse)); 
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
 