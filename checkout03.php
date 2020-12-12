<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>MIS 314 Sample Bookstore</title>
    <link rel="stylesheet" href="/sandvig/mis314/assignments/bookstore/StyleSheet.css" type="text/css" />
</head>

<body>


    <div id="pageContainer">
        <div id='checkoutContent'>
            <?php include 'header.php'; ?>

            <!-- start content -->

            <?php
            include "validationUtilities.php";
            include "encryption.php";
            include "databaseConnection.php";
            $link = fConnectToDatabase();

            include "ListAuthors.php";

            //template enhancement - start output capture
            ob_start();

            //*** a. Retrieve & validate form data ***//
            $email = fCleanString($link, $_POST["email"], 50);
            $fname = fCleanString($link, $_POST["fname"], 20);
            $lname = fCleanString($link, $_POST["lname"], 20);
            $street = fCleanString($link, $_POST["street"], 25);
            $city = fCleanString($link, $_POST["city"], 30);
            $state = fCleanString($link, $_POST["state"], 2);
            $zip = fCleanString($link, $_POST["zip"], 5);
            //$updateID = fCleanNumber($_GET['updateID']);

            $custIDe = $_POST["custIDe"];

            if (strlen($custIDe) > 0) {
                //returning customer
                $custID = decrypt($custIDe);
            } else {
                //new customer
                $custID = 0;
            }

            //set validation flag
            $IsValid = true;
            echo "<p class='centeredNotice'>";
            //email
            if (!fIsValidEmail($email)) {
                echo "Invalid email<br>";
                $IsValid = false;
            }
            //fname
            if (!fIsValidLength($fname, 2, 20)) {
                echo "Enter first name (2-20 characters)<br>";
                $IsValid = false;
            }
            //lname
            if (!fIsValidLength($lname, 2, 20)) {
                echo "Enter last name (2-20 characters)<br>";
                $IsValid = false;
            }

            if (!fIsValidLength($street, 2, 25)) {
                echo "Enter street (2-25 characters)<br>";
                $IsValid = false;
            }

            if (!fIsValidLength($city, 2, 30)) {
                echo "Enter city (2-30 characters)<br>";
                $IsValid = false;
            }

            if (!fIsValidStateAbbr($state)) {
                echo "Enter 2-character state abbreviation<br>";
                $IsValid = false;
            }

            if (!fIsValidZipCode($zip)) {
                echo "Enter 5-digit zip<br>";
                $IsValid = false;
            }

            /* captcha */
            if(isset($_POST['g-recaptcha-response'])){
                $captcha=$_POST['g-recaptcha-response'];
                if (!$IsValid) {
                    //at least one element not valid. Echo a message and stop execution
                    echo "
                    <input type='button' class='returnButton' value='<< Go Back <<' onClick='history.back()'><br>";
                    //stop execution. 
                    include 'footer.php';
                    exit();
                }
              }
              if(!$captcha){
                echo 'Please check the the captcha form.<br>';
                if (!$IsValid) {
                    //at least one element not valid. Echo a message and stop execution
                    echo "
                    <input type='button' class='returnButton' value='<< Go Back <<' onClick='history.back()'><br>";
                    //stop execution. 
                    include 'footer.php';
                    exit();
                }
              }
              $secretKey = "6LfZyqIZAAAAAL_ruXlsVCnerfTWq-S23tdhg5fE";
              $ip = $_SERVER['REMOTE_ADDR'];
              // post request to server
              $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
              $response = file_get_contents($url);
              $responseKeys = json_decode($response,true);
              // should return JSON with success as true
              if($responseKeys["success"]) {
                      echo 'Thanks for your order!<br>';
              } else {
                      echo 'You are spammer!<br>';
                      echo "
                        <input type='button' class='returnButton' value='<< Go Back <<' onClick='history.back()'><br>";
                      include 'footer.php';
                      exit;
              }
            
            echo "</p>";

            //*** b. Write customer data to database ***//
            if ($custID == 0) {
                $sql = "Insert into bookCustomers (email, fname, lname, street, city, state, zip)
                            VALUES ('$email', '$fname', '$lname', '$street', '$city', '$state', '$zip')";
                $result = mysqli_query($link, $sql) or die('Insert error: ' . mysqli_error($link));

                $custID = mysqli_insert_id($link);
                $custIDe = encrypt($custID);
            } else {
                //returning customer syntax for an update statement
                $sql = "Update bookCustomers set email='$email', fname='$fname', lname='$lname', street='$street', city='$city', state='$state', zip='$zip' 
                    WHERE custID=$custID";
                mysqli_query($link, $sql) or die('Update error: ' . mysqli_error($link));
            }

            //*** c. Write order into database ***//
            $cookieName = "myCart2";
            if (isset($_COOKIE[$cookieName])) {
                $bookArray = unserialize($_COOKIE[$cookieName]);
            }
            //delete cookie
            setcookie($cookieName, null, time() - 60000);

            if (isset($bookArray)) {
                //add record to bookorders
                $sql = "insert into bookorders(custID, orderdate) Values($custID, " . time() . ")";

                mysqli_query($link, $sql);
                $orderID = mysqli_insert_id($link)or die('Insert error: ' . mysqli_error($link));

                //write  bookorder items
                foreach ($bookArray as $ISBN => $qty) {
                    $discount = 0.8;
                    $sql = "INSERT INTO bookorderitems (orderID, ISBN, qty, price) 
                    VALUES ($orderID, '$ISBN', $qty, (select (price * $discount) from bookdescriptions where ISBN = '$ISBN'))";
                    
                    mysqli_query($link, $sql)or die('Insert error: ' . mysqli_error($link));
                }
            }
            //*** d. Confirmation to browser ***//
            $html = "<div class='pageTitle'>Order Confirmation</div>
<br>
<table id='cart'>
    <tbody>
        <tr>
            <td class='boldLabel'>
                Order Number:
            </td>
            <td>$orderID</td>
        </tr>
        <tr>
            <td valign='top' class='boldLabel'>
                Shipping Address:
            </td>
            <td>$fname $lname<br>
                $street<br>
                $city, $state $zip<br>
            </td>
        </tr>
        <tr>
            <td valign='top' class='boldLabel'>
                Books Shipped:
            </td>";
            if (isset($bookArray) && count($bookArray) > 0) {

                $sql = 'SELECT ISBN, title, price  
                FROM bookdescriptions
                where ';

                foreach ($bookArray as $isbn => $qty) {
                    $sql .= " isbn = '$isbn' or ";
                }
                $sql = substr($sql, 0, strlen($sql) - 3);

                //echo "sql: $sql <br>";

                //$result is an array containing query results
                $result = mysqli_query($link, $sql)
                    or die('SQL syntax error: ' . mysqli_error($link));

                $subtotal = 0;
                $html .= "<table id='cart'>"
                    . "<tr><th>Title</th>
                   <th>Qty</th>
                   <th>Price</th>
                   <th>Total</th>
                   </tr>";
                while ($row = mysqli_fetch_array($result)) {
                    //Field names are case sensitive and must match
                    //the case used in sql statement
                    $isbn = $row['ISBN'];
                    $price = $row['price'];
                    $qty = $bookArray[$isbn];
                    $total = $qty * $price;
                    $subtotal += $total;
                    $html .= "
                      <tr>
                      <td>
                         <a class='booktitle' href='ProductPage.php?isbn=$isbn'>$row[title]</a> </td>
                      <td>$qty</td>
                      <td>$" . number_format($price, 2) . "</td>
                      <td>$" . number_format($total, 2) . "</td>
                   </tr>";
                }
                $html .= "</table>";
                $baseshipping = 3.49;
                $unitshipping = .99;
                $shipping = $baseshipping + (($totalbooks - 1) * $unitshipping);

                $html .= " <br />

             <table class='cartTotal'>
               <tr>
                 <td> Sub-Total:</td>
                 <td align='right'>$" . number_format($subtotal, 2) . "</td>
               </tr>
                   <tr>
                 <td> Shipping:*</td>
                 <td align='right'>$" . number_format($shipping, 2) . "</td>
               </tr>
               <tr>
                 <td><b>Total:</b></td>
                 <td align='right'><b>$" . number_format($subtotal + $shipping, 2) . "</b></td>
               </tr>
             </table>";
                echo $html;
                //*** e. Email confirmation ***//
                $message = "
             <html>
                <head>
                    <titles>Confirmation from Best Books</titles>
                    <style type='text/css'>
                        .text-center {text-align: center;}
                        table {border:solid 1px;}
                        td {text-align:left; background-color:#eeeeee; padding:3px;}
                        .title{font-size:large; color:red;}
                    </style>
                    <head>
                    <body class='text-center'>"
                    . $html . "</body></html>";

                // To send HTML mail, the Content-type header must be set
                $fromDisplayName = "GeekBooks";
                $fromEmail = "murray39@wwu.edu";

                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: $fromDisplayName <$fromEmail >" . "\r\n";

                // Mail it
                $toEmail = $email;
                $subject = "Order Confirmation from Geek Books";

                if (fIsValidEmail($toEmail) && fIsValidEmail($fromEmail)) {
                    //workaround for using display name with php on Window's servers
                    ini_set('sendmail_from', "<$fromEmail>");

                    //Send email & retrieve status
                    $IsSent = mail($toEmail, $subject, $message, $headers);

                    //Status message (message accepted by SMTP server, may still fail for other reasons).
                    if ($IsSent)
                        $status = "HTML email sent to $toEmail";
                    else
                        $status = "Email NOT sent to $toEmail";
                } else {
                    $status = "Please enter email address for recipient and sender";
                }

                //*********** helper functions *********
                function ValidEmail($address)
                {
                    $pattern = "^[\w!#$%*_\-/?|\^\{\}'~\.]+@(\w+\.)+\w{2,4}^";
                    return preg_match($pattern, $address);
                }

                function fGet($input)
                {
                    if (!empty($_GET[$input]))
                        return $_GET[$input];
                    else
                        return "";
                }
            }
            ?>
            <div class='cartIcons'>
                A confirmation has been sent to your email address.<br> Thank you for shopping with GeekBooks.com.<br>
                <a href='index.php'> <img border='0' src='/sandvig/mis314/assignments/bookstore/images/continue-shopping.gif' width='121' height='19' alt='Continue shopping' /></a>&nbsp;&nbsp;&nbsp;&nbsp;<br><br>
                <form method='post' action='orderHistory.php' class='centeredText'>
                    <input type='submit' class='button' value='View Your Order History' style='max-width: 300px;' />
                    <input type='hidden' name='custIDe' value='<?php echo $custIDe ?>'/>
                </form>
            </div>
        </div><br><br>
        <!-- end content -->
        <?php include 'footer.php'; ?>
    </div>
    </div>
    <!-- Sample site uses a MasterPage-like template for page layout. -->
    <!-- This is not required. It may be used as an enhancement. -->
    <!-- Source: http://spinningtheweb.blogspot.com/2006/07/approximating-master-pages-in-php.html -->
</body>

</html>