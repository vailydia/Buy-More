

<?php
session_start();
include_once ('lib/csrf.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BuyMore</title>

    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <link rel="stylesheet" href="bootstrap-social.css"/>
    <link href="mystyles.css" rel="stylesheet"/>
    <link href="incl/admin.css" rel="stylesheet" type="text/css"/>

</head>



<body>

  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
          <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainnavbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand logo-link" ui-sref="app"><img src="images/logo.png" height=35 width=80></a>
          </div>
          <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                  <li><a class="active" href="index.php">Home</a>
                  <li><a class="active" href="admin.php">Admin Panel</a></li>
                  <li><a class="active" href="#">About</a></li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                  <li><a class="active" href="login.php"><span>Login</span></a></li>
                  <li><a id="userInfo" href="login.php">Hello:
                    <?php
                       if(!empty($_COOKIE['t4210'])){
                         if($t = json_decode(stripslashes($_COOKIE['t4210']),true)) {
                             if($t['em']){
                                echo $t['em'];
                             }
                         }
                       }else{
                          echo "Guest";
                       }
                    ?></a>
                  </li>

                   <li><a><form id="logoutForm" method="POST" action="auth-process.php?action=<?php  echo ($action='logout');  ?>">
                    <input type="hidden" name="nonce" value="<?php  echo csrf_getNonce($action);   ?>" />
               			<input type="submit" value="Logout" />
               		</form></a></li>

              </ul>
          </div>
      </div>
  </nav>

  <div class = "header">
    <div class="container">
        <div class="row row-header">
            <div class="col-xs-12 col-sm-8">
                <h1 id="logo">BUY MORE</h1>
                <h3>WELCOME TO YOUR OWN SHOPPING MALL!</h3>
            </div>
            <div class="col-xs-12 col-sm-2">
            </div>
            <div class="col-xs-12 col-sm-2">
            </div>
        </div>
    </div>
  </div>


  <!-- main content -->
  <div class="container">

      <div class="row row-content">
        <div class="col-xs-12 col-sm-2 col-sm-push-9">
          <div class="shoppingList btn">
            <button class="btn" id="listButton">Shopping List</button>
            <div class="dropdown-content">

              <form id="checkout_form" method="POST" action="<?php echo ($pay_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr'); ?>" onsubmit="return false;">

                <table id="shopping-cart"></table>
                <input type="hidden" name="cmd" value="_cart"/>
                <input type="hidden" name="upload" value="1" />

                <!-- Identify your business so that you can collect the payments. -->
                <input type="hidden" name="business" value="<?php echo ($paypalID='vailydia-facilitator@hotmail.com'); ?>"/>

                <!-- some setting. -->
                <input type="hidden" name="lc" value="HK"/>
                <input type="hidden" name="currency_code" value="HKD"/>
                <input type="hidden" name="charset" value="utf-8"/>

                <!-- get from server side：custom, invoice, item_name, item_number, quantity, amount. -->
                <input type="hidden" name="custom" value="0"/>
                <input type="hidden" name="invoice" value="0"/>

                <p id="sum"></p>
                <input id="btncheckout" type="submit" value="Checkout" onclick="submitShoppingCart();"/>

              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row row-content">

  <!-- navigation list(leftsize) -->
          <div class="col-xs-12 col-sm-3">
            <nav>
                <div class="container-fluid">
                    <ul id="subnavbar" class="nav nav-pills nav-stacked">

                    </ul>
                </div>
            </nav>
          </div>
   <!-- breadcrumb -->
          <div class="col-xs-12 col-sm-7">

            <div class="row">
                <div class="col-xs-12">
                   <ul id="breadcrumbDetails" class="breadcrumb">

                   </ul>
                </div>
            </div>

            <div class = "contentright">
              <ul id="productListDetails" class="productTable">

              </ul>
            </div>


          </div>

      </div>

      <!-- test the admin panel -->
      <div class="row row-content">
        <div id="adminPaneldiv">
          <!--
          <button id = "adminPanel" class="btn" data-toggle="modal" data-target="#myModal">
          	Open Admin Panel
          </button>
          -->
          <div id="adminPanel">
          	<a href="admin.php">Open Admin Panel</a>
          </div>
        </div>
      </div>

  </div>



  <footer class="row-footer">
      <div class="container">
          <div class="row">
              <div class="col-xs-12 col-sm-6 col-xs-push-3">
                  <h5>Our Address</h5>
                  <address>
                12345, Shopping Road<br>
                Fanling, HONG KONG<br>
                <i class="fa fa-phone"></i>: +852 1234 5678<br>
                <i class="fa fa-envelope"></i>:
                       <a href="mailto:buymore@shopping.net">
                       buymore@shopping.net</a>
                  </address>
              </div>

              <div class="col-xs-12 col-sm-6">

                  <div>
                      <br><br><br><br>
                      <a class="btn btn-social-icon btn-facebook" href="http://www.facebook.com/"><i class="fa fa-facebook"></i></a>
                      <a class="btn btn-social-icon btn-twitter" href="http://twitter.com/"><i class="fa fa-twitter"></i></a>
                      <a class="btn btn-social-icon btn-youtube" href="http://youtube.com/"><i class="fa fa-youtube"></i></a>
                  </div>
              </div>
              <div class="col-xs-12">
                  <p align=center>© Copyright 2017 Buy More</p>
              </div>
          </div>
      </div>
  </footer>

<script src="bower_components/jquery/dist/jquery.min.js" rel="stylesheet" type = "text/javascript"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="incl/myLib.js" type = "text/javascript"></script>
<script src="myjs.js" type = "text/javascript"></script>

</body>


</html>
