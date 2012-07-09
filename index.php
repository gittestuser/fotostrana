<?php
  /*************
  | Welcome to TinyCMS 1.4
  | I spent an evening cleaning up the code
  | and added tons of comments so even novice developers
  | can modify TinyCMS to their liking!
  * www.TinyCMS.net
  *************/
  
  error_reporting(0);

  // TinyCMS includes
  include("inc/config.php");
  include("inc/functions.php");
  include("inc/custom_conf.php");

  // If the requested page is the admin panel
  if ($_GET['view'] == "admin"){
    include("admin/admin.php");
  } else {
  
    // To show pages
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    	ob_start();
    	@include 'tpl/wsl.tpl';
    	$page_content = ob_get_contents();
    	ob_end_clean();
    
    	echo $page_content;
    }
    else {
    	header('content-type: application/json; charset=utf-8');
    
    	$page_data = array('pageName' => $_GET['page'], 'content' => '');
    
    	ob_start();
    	@include 'tpl/' . $_GET['page'] . '.html';
    	$page_data['content'] = ob_get_contents();
    	ob_end_clean();
    
    	echo json_encode($page_data);
    }
  
  }


    //new branch
?>