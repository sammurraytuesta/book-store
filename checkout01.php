
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <title>Sign-in - GeekBooks - MIS 314 Sample Bookstore</title>
      <link rel="stylesheet" href="/sandvig/mis314/assignments/bookstore/StyleSheet.css" type="text/css" />
      <!-- captcha -->
      <script src='https://www.google.com/recaptcha/api.js' async defer></script>
   </head>
   <body>

 <?php include 'header.php';?>

      <div id="pageContainer">
         <!-- start content -->
         <div id="checkoutContent">
            
<div class="pageTitle">Your Account</div>
<p class="pageTitle2">Buying online is quick and easy!</p>
<p class="pageTitle2">  
<?php
$totalbooks = $_COOKIE["BookCount"];
echo $totalbooks . " item";
if ($totalbooks != 1)
   echo 's';
echo ' in your cart';
?>
</p>
   <form method="post" action="checkout02.php" autocomplete="on" class="myForm">
      <div class="cartIcons">
      <div class="formGroup">
         <label for="email">Email:</label>
         <input type="email" name="email" id="email" autofocus required placeholder="Email"  />
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
           <label> </label>
         <input type="image" src="/sandvig/mis314/assignments/bookstore//images/proceed-to-checkout.gif" alt="Proceed to checkout" class="inputImage" />
      </div>
      </div>
   </form>
    
         </div> 
         <!-- end content -->

<?php include 'footer.php';?>
      </div>

      <!-- Sample site uses a MasterPage-like template for page layout. -->
      <!-- This is not required. It may be used as an enhancement. -->
      <!-- Source: http://spinningtheweb.blogspot.com/2006/07/approximating-master-pages-in-php.html -->
   </body>
</html>


