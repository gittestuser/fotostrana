<?php
/*************
| Welcome to TinyCMS 1.4
| Drafts & improved templating added
* www.TinyCMS.net
*************/
?>
<!doctype html>
<html>
<head>
<title><?php echo $title; ?> - Admin</title>
<link rel="stylesheet" type="text/css" href="css/admin.css" />
<?php if ($_GET['do'] != "settings"){ ?>
<!-- Include required javascript files -->
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="js/qtip.js"></script>
<script type="text/javascript" src="js/qtip_init.js"></script>
<script type="text/javascript" src="js/tiny_mce_init.js"></script>
<!-- End javascript includes -->
<?php } ?>
</head>
<body>
  <div id="wrap">
  <?php

    /***************
    | Below we'll check if the user is logged in or not.
    | If they are we'll show them the admin panel.. Else we'll show them the login page.
    ***************/

    if ($_SESSION['security'] == md5($_SERVER['HTTP_USER_AGENT']) && $_SESSION['username'] != null) {
    
    /***************
    | Below we'll check if the user wishes to logout or not...
    | If they do, we'll unset their session and redirect them.
    ***************/

    if ($_GET['do'] == "logout"){
      
      // Unset the username session
      unset($_SESSION['username']);
      
      // Unset the security session
      unset($_SESSION['security']);
      
      // Redirect back to the admin login page with a success message
      echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&login=quit\">";
      
      // exit
      exit();

    } // end logout

  ?>

  <!-- Page Title -->
  <div id="title">
    <a href="../admin/"><?php echo $title; ?> Admin</a>
    <span style="font-size:13px;">
      (<a href="../#home" style="color:#808080; text-decoration:none;" target="_blank">Visit</a>)
    </span>
  </div> <!-- end page title -->

  <!-- Main Content Area -->
  <div id="box">
    <?php
            
    /***************
    | If $_GET['do'] is null (blank), the user is not on a page
    | therefore we can show them the admin panel..
    ***************/

     // Check if DO is blank or not
     if ($_GET['do'] == null){
       
    ?>
    
    <!-- Admin Panel Links -->
    <h2 style="font-size:14px;">Admin Options</h2>
    <a href="?view=admin&do=pages&create=new" rel="modal" tooltip="Create a new page"><img src="ico/new_page.png"></a>
    <a href="?view=admin&do=pages" rel="modal" tooltip="View a list of pages and manage site pages"><img src="ico/pages.png"></a>
    <a href="?view=admin&do=settings" rel="modal" tooltip="Modify site settings"><img src="ico/settings.png"></a>
    
    <!-- TinyCMS News -->
    <h2 style="font-size:14px;">TinyCMS News</h2>
    <?php 
    // 1.1 administration panel updates
    /************************************/
    $timeouts = stream_context_create(array(
        'http' => array(
            'timeout' => 2 // Seconds to try and load the patch notes & version file
        )
    ));
    $version_check = @file_get_contents('http://tinycms.net/odc_news/versions.txt', 0, $timeouts);
    if ($version_check != $ver_num){ echo "<div id=\"error\" style=\"margin-bottom:3px; color:#BC4040;\">There's a new patch available for TinyCMS</div>"; }
    echo @file_get_contents('http://tinycms.net/odc_news/news.php', 0, $timeouts);
    /************************************/
    ?>

    <?php
    
    /***************
    | If $_GET['do'] is not null, we can show the user another page.
    ***************/

    } elseif ($_GET['do'] == "pages"){

      // Show the page manager
      adminPages();

    } elseif ($_GET['do'] == "settings"){

      // Show the settings manager
      showSettings();

    }

    ?>

  </div><!-- end main content area -->

  <?php
  
    /***************
    | If the user is not logged in,
    | we'll show the login forms
    ***************/

    } else {

    // If the login form has been submitted, we'll check the login details
    if ($_GET['login'] == "1"){
      
      // Check if the posted username and password matches the username and password in
      // custom_conf.php

      if (($_POST['username'] == $user) && ($_POST['password'] == $pass)){
        
        // The user/pass match, we'll set the sessions
        $_SESSION['username'] = "admin/true";
        
        // This will probably come in use at a future date for added security, but at this time
        // it doesn't really serve a purpose.

        $_SESSION['security'] = md5($_SERVER['HTTP_USER_AGENT']);
        
        // Success! Redirect the user back to the admin area.
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin\">";
        
        // exit
        exit();
    
      } else {
        
        // The login was incorrect, show an error message
        echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&login=false\">";
        
        // exit
        exit();

      }

    } else {
    
    /***************
    | The form has not yet been submitted, we can now show the login form.
    ***************/

  ?>

    <!-- Page title -->
    <div id="login_title">
      <?php echo $title; ?> Admin
    </div><!-- end page title -->

    <div id="box">

      <?php
      
      /***************
      | Below are the error and success messages for the login page
      ***************/

      if ($_GET['login'] == "false"){
        
        // The username and password were not correct
        echo "<div id=\"error\">The username and/or password you entered was incorrect.</div>";

      }

      if ($_GET['login'] == "quit"){

        // You successfully logged out
        echo "<div id=\"success\">You are now logged out.</div>";

      }

      ?>

      <!-- Start the form -->

      <form action="?view=admin&login=1" method="post">
        Username<br />
        <input type="text" name="username" autocomplete="off" size="30">
        <br /><br />
        Password<br />
        <input type="password" name="password" autocomplete="off" size="30">
        <br /><br />
        <input type="submit" value="Login">
      </form>

      <!-- End the form -->

    </div>

    <?php
      } // End login form
      } // End login form check
    ?>

  <div id="footer">
    <div id="left">
      <!-- Please support the development of TinyCMS by donating - Please do not remove the link from the back-end, you may remove the link from the front-end -->
      Powered by TinyCMS <?php echo $ver_num; ?>
    </div>  

    <?php
    if ($_SESSION['security'] == md5($_SERVER['HTTP_USER_AGENT']) && $_SESSION['username'] != null) {
    ?>

    <div id="right">
      <a href="?view=admin&do=logout">Logout</a>
    </div>

    <?php
    }
    ?>

  </div>
  </div>
</body>
</html>
