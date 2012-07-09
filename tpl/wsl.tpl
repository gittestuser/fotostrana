<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $title; ?></title>
		
		<link rel="stylesheet" type="text/css" href="themes/default/css/layout.css" />
		
		<script type="text/javascript" src="admin/js/jquery-1.6.2.min.js"></script>  
		
		<script type="text/javascript">
		jQuery(document).ready(function() {
			function request(page) {
				jQuery('#content').html('<h2 class="mainHeading">Loading</h2><p>Requesting content from server, please wait.</p>');

				jQuery.ajax({
					url: 'index.php',
					type: "GET",
					data: "page=" + page,
					timeout: 15000
				})
				.error(function(xhr, status) {
					var errorMsg = '<p>Error loading the page!</p>';
					
					if(null != status) {
						errorMsg = 'Sorry, but "' + status + '" occured!';
					}

					jQuery('#content').html('<h2 class="mainHeading">Error</h2>' + errorMsg);
				})
				.success(function(data) {
					jQuery('<?php echo genNav(); ?>').removeClass('active');

					jQuery('a[href="#' + data.pageName + '"]').addClass('active');

					jQuery('#content').html(data.content);
				});
			}

			jQuery('<?php echo genNav(); ?>').click(function(e) {
				e.preventDefault();

				var pageName = jQuery(this).attr('href').split("#")[1];

				request(pageName);
			});

			request('home');
		});
		</script>
	</head>
	
	<body>
	
		<!-- WRAPPER -->
		<div id="wrap">
	
			<div id="header"><?php echo $title; ?></div>
		
			<!-- NAVIGATION -->
			<div id="nav_bar">
                        <a href="#home" class="active">Home</a>
                        <?php echo pages(); ?>
                        </div>
			<!-- END NAVIGATION -->
			
			<!-- MAIN CONTENT -->
			<div id="content">
				<!-- PAGE CONTENT LOADED VIA AJAX -->
			</div>
			<!-- END MAIN CONTENT -->
			
			<div id="footer">
			  <div id="left">
			    <?php echo $title; ?> all rights reserved
			  </div>
			  <!-- Please support the development of TinyCMS by donating - You may remove the footer link from the front end, please do not remove the link from the backend -->
			  <div id="right">
			    Powered by <a href="http://tinycms.net" target="_blank">TinyCMS</a>
			  </div>
	                </div>
			
		</div>
		<!-- END WRAPPER -->
		
	</body>
</html>