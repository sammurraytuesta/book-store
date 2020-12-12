<?php
//include database connection
include("databaseConnection.php");

//connect to database
$link = fConnectToDatabase();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Shipping Information - GeekBooks - MIS 314 Sample Bookstore</title>
    <link rel="stylesheet" href="/sandvig/mis314/assignments/bookstore/StyleSheet.css" type="text/css" />
    <!-- captcha -->
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
</head>

<body>
    <?php include 'header.php';?>

    <div id="pageContainer">
        <!-- start content -->
        <div id="checkoutContent">

            <!-- start page content *************** -->


            <div class="pageTitle">Shipping Information</div>
            <?php
include 'validationUtilities.php';
include 'encryption.php';
//set validation flag
$IsValid = true;
echo "<p class='centeredNotice'>";
//email
$email = fCleanString($link, $_POST['email'], 50);
if (!fIsValidEmail($email)) {
    echo "Invalid email<br>";
    $IsValid = false;
}
/* captcha */
if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
          if (!$IsValid) {
            //at least one element not valid. Echo a message and stop execution
            echo "
            <p class='centeredNotice'><input type='button' class='button' style='text-align: center' value='<< Go Back <<' onClick='history.back()'><br></p>";
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
                echo 'Thanks for entering your email!<br>';
        } else {
                echo 'You are spammer!<br>';
        }
echo "</p>";

//List records
$sql = "SELECT custID, fname, lname, email, street, city, state, zip
FROM bookCustomers where email = '$email'";

//$result is an array containing query results
$result = mysqli_query($link, $sql)
or die('SQL syntax error: ' . mysqli_error($link));

if (mysqli_num_rows($result) == 0 ) {
echo "<p class='centeredNotice'>New Customer - Please provide your shipping address.</p>";
}
else {
echo "<p class='centeredNotice'>Returning Customer - Please confirm your mailing and e-mail addresses.</p>";
$row = mysqli_fetch_array($result);
$custIDe = encrypt($row['custID']);
}
?>
            <form method="post" action="checkout03.php" autocomplete="on" class="myForm">

                <div class="formGroup">
                    <label for="email">
                        Email: </label>
                    <input type="email" name="email" value="<?php echo $email;?>" required placeholder="Enter Email"
                        maxlength="50" />
                </div>

                <div class="formGroup">
                    <label for="fname">
                        First name: </label>
                    <input type="text" name="fname" autofocus required value="<?php echo $row['fname'];?>"
                        placeholder="First name" title="first name" maxlength="20" pattern="[A-Za-z'-]{2,20}" />
                </div>
                <div class="formGroup">
                    <label for="lname">
                        Last name: </label>
                    <input type="text" name="lname" required value="<?php echo $row['lname'];?>" placeholder="Last name"
                        title="last name" maxlength="20" pattern="[A-Za-z'-]{2,20}" />
                </div>
                <div class="formGroup">
                    <label for="street">
                        Street: </label>
                    <input type="text" name="street" required value="<?php echo $row['street'];?>"
                        placeholder="Street address" title="street address" maxlength="25" />
                </div>
                <div class="formGroup">
                    <label for="city">
                        City:</label>
                    <input type="text" name="city" required value="<?php echo $row['city'];?>" placeholder="City"
                        title="city" maxlength="30" pattern="[A-Za-z'-]{2,30}" />
                </div>
                <div class="formGroup">
                    <label for="state">
                        State:</label>
                    <td>
                        <input type="text" name="state" style="width:40px" required value="<?php echo $row['state'];?>"
                            placeholder="ST" title="2-character state abbreviation" max length="2"
                            pattern="[A-Za-z]{2}" />
                </div>
                <div class="formGroup">
                    <label for="zip">
                        Zip: </label>
                    <input type="text" name="zip" style="width:80px;" required value="<?php echo $row['zip'];?>"
                        placeholder="Zip" title="zip" maxlength="5" pattern="[0-9]{5}" />
                </div>
                <div class="text-center">
                     <div class="g-recaptcha" data-sitekey="6LfZyqIZAAAAAM0x3uIQBW5vhKTphuMJtkFSK4TM"></div>
                </div>
                <style>
                    .text-center {
                        text-align: center;
                    }

                    .g-recaptcha {
                        display: inline-block;
                    }
                </style>
                <div class="formGroup">
                    <label></label>

                    <input type="hidden" name="custIDe" value="<?php echo $custIDe;?>">
                    <input class="inputImage" type="image"
                        src="/sandvig/mis314/assignments/bookstore//images/buy-now.gif">
                </div>
            </form>
            <?php
   
   ?>
            <br>
            <!-- must use method post to transfer encrypted custID. Cannot transfer in query string due to URL encoding -->
            <form method='post' action='orderHistory.php' class='centeredText' >
            <input type='submit' class='button' value='View Your Order History' style='max-width: 300px;' />
            <input type='hidden' name='custIDe' value='<?php echo $custIDe; ?>' />
             </form>
            <!-- end page content *************** -->
        </div>
        <!-- end content -->

        <?php include 'footer.php';?>
    </div>

    <!-- Sample site uses a MasterPage-like template for page layout. -->
    <!-- This is not required. It may be used as an enhancement. -->
    <!-- Source: http://spinningtheweb.blogspot.com/2006/07/approximating-master-pages-in-php.html -->
</body>

</html>