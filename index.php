<?php
  if (count($_GET)<3)
  {
      echo " No Params in URL \n";
      echo "<br> "+$_SERVER['SERVER_ADDR'];
      echo "<br> "+$_SERVER['HTTP_HOST'];
      exit();
  }
  $action=$_GET["act"];
  $name=$_GET["name"];
  $pass=$_GET["pass"];
  $ipAdd=$_SERVER['REMOTE_ADDR'];
  $status =0;
  if(count($_GET)>3)
      $status=$_GET["st"];
$servername = "localhost";
$username = "id2578885_rzip";
$password = "edcxsw22";
$dbname = "id2578885_ipdb";
//*
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


$table ='Users';
if ($result = $conn->query("SHOW TABLES LIKE '$table'")) {
    if($result->num_rows == 0) {
  //--      echo "Table  does notexist";
// sql to create table
    $sql = "CREATE TABLE $table (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            username VARCHAR(30) NOT NULL,
            password VARCHAR(30) NOT NULL,
            ipAdd VARCHAR(30) NOT NULL,
            info VARCHAR(50),
            info1 VARCHAR(50),
            st int,
            y int,
            reg_date int
            )";

    if ($conn->query($sql) === TRUE) {
      //-- echo "Table MyGuests created successfully" ."<br>";
    } else {
       echo "Error creating table: " . $conn->error ."<br> \n";
       }

  }
  else {
//--    echo "Table  exist <br>";
  }
}

$sql1 = "SELECT id, username, ipAdd ,reg_date,st  FROM $table WHERE username='$name'";
/* @var $result1 type */
$result1 = $conn->query($sql1);

if($action=== "Reg")
{
if ($result1->num_rows === 0 )
    {
           $status=1;
           $ttmm=time()+3600*3;
     $sql = "INSERT INTO $table (username,password, ipAdd , st , reg_date)
     VALUES ('$name','$pass','$ipAdd' , $status , $ttmm )";

    if ($conn->query($sql) === TRUE) {
       echo "<br>"."New record created successfully \n";
    } else {
            echo "<br>"."Error: " . $sql . "<br>" . $conn->error;
           }
}else{
    echo "<br> unavailable user name";
}

exit();
}    


if($action === "Get")
{
   if ($result1->num_rows === 0 ) {
       echo " Member not found \n";
   }else{
       $row = $result1->fetch_assoc();
       echo "<br>".$row["ipAdd"]."\n";
       echo "<br>".$row["st"]."\n";
       echo "<br> ".$row["reg_date"];
       $st_=" ";
       $sstt=$row["st"];
       $ct=time()+3600*3;
       echo "<br>".$ct."\n";
       if($ct-$row["reg_date"]>3600)
           $sstt=0;
       if(($ct-$row["reg_date"]>60)&&($sstt==1))
          $sstt=0;
       switch ($sstt)
       {
           case 0: $st_="Offline";
               break;;
           case 1: $st_="Idle";
               break;;
           case 2: $st_="Busy";
               break;;
           case 3: $st_="Unknown";
               break;;
           case 4: $st_="last";
               break;;
       }
       echo "<br>".$st_."\n";
    }
       
   $conn->close();
   exit(); 
}
$sql1 = "SELECT id, username, password , ipAdd ,reg_date,st  FROM $table WHERE username='$name' AND password='$pass'";
/* @var $result1 type */
$result1 = $conn->query($sql1);

if ($result1->num_rows !== 0 )
{
    $ttmm = time()+3600*3;
    
   $sql = "UPDATE $table SET ipAdd='$ipAdd',st=$status , reg_date=$ttmm WHERE username='$name' AND password='$pass'";

   if ($conn->query($sql) === TRUE) {
      echo "<br>Record updated successfully : ".$ipAdd."\n"." s=".$status;
   } else {
       echo "Error updating record: " . $conn->error;
   }

}
else{
    echo "<br> Error user/pass.";
}

/*
$sql1 = "SELECT id, username, ipAdd ,reg_date,st  FROM $table WHERE username='$name'";
$result = $conn->query($sql1);
$cc=0;
if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>"." No".str_repeat("&nbsp;", 15)."Name".str_repeat("&nbsp;", 30)
            ."IP".str_repeat("&nbsp;", 60)." Time".str_repeat("&nbsp;", 5)." x";
    echo "<br>------------------------------------------------------------------------------------------------------------------------";
    while($row = $result->fetch_assoc()) {
        echo  "<br>". $row["id"].str_repeat("&nbsp;", 15). $row["username"].str_repeat("&nbsp;", 20)
                . $row["ipAdd"].str_repeat("&nbsp;", 40).$row["reg_date"]
                .str_repeat("&nbsp;", 10).$row["st"];
        if($row["username"]===$name){
          $cc++; 
        }else {}
        
    }
} else {
    echo "0 results";
}
//*/
$conn->close();
//*/
?>