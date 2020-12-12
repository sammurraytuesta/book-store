<?php while ($row = mysqli_fetch_array($result)){  
   //Retrieve parameters from querystring and sanitize
   $fname = fCleanString($link, $_GET['fname'], 15);
   $lname = fCleanString($link, $_GET['lname'], 15);
   $street = fCleanString($link, $_GET['street'], 15);
   $city = fCleanString($link, $_GET['city'], 15);
   $state = fCleanString($link, $_GET['state'], 15);
   $zip = fCleanString($link, $_GET['zip'], 15);
   $updateID = fCleanNumber($_GET['updateID']);
   $updateID2 = fCleanNumber($_GET['updateID2']);

   //Insert
   if (!empty($fname) && !empty($lname) && !empty($street) && !empty($city) && !empty($state) && !empty($zip) && empty($updateID2)) {
      $sql = "Insert into bookCustomers (lname, fname, street, city, state, zip)
             VALUES ('$lname', '$fname', '$street', '$city', '$state', '$zip')";
      mysqli_query($link, $sql) or die('Insert error: ' . mysqli_error($link));
   }
   
   //Update
   if (!empty($updateID)) {
    $sql = "select custID, fname, lname, street, city, state, zip from bookCustomers 
    WHERE custID=$updateID";
    $result = mysqli_query($link, $sql) or die('Update error: ' . mysqli_error($link));
    $row = mysqli_fetch_array($result);
    $fname = $row['fname'];
    $lname = $row['lname'];
    $street = $row['street'];
    $city = $row['city'];
    $state = $row['state'];
    $zip = $row['zip'];
    }

     //Update2
     if (!empty($updateID2)) {
        $sql = "Update bookCustomers set fname='$fname', lname='$lname', street='$street', city='$city', state='$state', zip='$zip' WHERE custID=$updateID2";
        mysqli_query($link, $sql) or die('Delete error: ' . mysqli_error($link));
     }
}

$orderID = fCleanstring($link, $_GET["orderID"], 15);
$nameF = fCleanString($link, $_GET['nameF'], 15);
$nameL = fCleanString($link, $_GET['nameL'], 15);
$name = fCleanString($link, $_GET['NAME'], 15);
$title = fCleanString($link, $_GET['title'], 15);
$ISBN = fCleanString($link, $_GET['ISBN'], 15);
$qty = fCleanNumber($_GET['qty']);
$orderdate = fCleanNumber($_GET['orderdate']);
?>