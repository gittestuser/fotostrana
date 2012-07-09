<?php
  /*************
  | Welcome to TinyCMS 1.4
  | Drafts & improved templating added
  * www.TinyCMS.net
  *************/
  
  error_reporting(0);

  // Get page
  $curr_page = $_GET['page'];
  
  /*************
  | The pages function
  | Creates a list of all pages (Excluding home & wsl.tpl)
  *************/

  function pages() {
    if ($handle = opendir('tpl')) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          $file = substr($file, 0, -(strlen($file)-(strrpos($file, "."))));
          if (($file == "wsl") || ($file == "home")){ } else {
            echo "<a href=\"#$file\" class=\"active\">" . ucfirst($file) . "</a>";
          }
        }
      }
      closedir($handle);
    }
  }
  
  /*************
  | The genNav function
  | Used in WSL.tpl for the javascript
  *************/
  
  function genNav() {
    if ($handle = opendir('tpl')) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          $file = substr($file, 0, -(strlen($file)-(strrpos($file, "."))));
          if ($file == "wsl"){ } else {
            echo "a[href=\"#$file\"],";
          }
        }
      }
      closedir($handle);
    }
  }

  /*******************
  | Below are the TinyCMS admin functions
  | It is not recomennded that you modify the below unless you're
  | familiar with the PHP language...
  | Always make a backup!
  *******************/

  # -> The adminPage() function allows us to edit, create, delete & rename pages!
  function adminPages() {
    # -> Set our global variables
    global $url;

    if ($_GET['rename'] != null){
      /*************
      | TinyCMS 1.2 introduced the rename feature.
      | Let's continue, firstly we'll get the title of the page you
      | wish to rename.
      *************/

      $title = $_GET['rename']; // Using $_GET, we'll "GET" the page name

      /*************
      | At this time, you cannot rename the home-page, or delete it.
      | For this reason, we must check if you're trying to rename the home-page or not.
      | If you are, an error will be displayed, if you aren't, you can continue with the rename.
      *************/

      if ($title == "home"){

        // Show the error message, you're trying to rename the front-page
        echo "Sorry, you are unable to rename the home-page due to the way we load our front page.";

      } else {

        /*************
        | So it seems you're not trying to rename the home-page!
        | Now we must check if the page you're trying to rename actually exists...
        | If it does, once again we can continue, or not...
        *************/

        if (file_exists("tpl/$title.html")) { // Check if the file exists on your server (in the tpl folder)

          // The file does exist, so we can now check if you have submited your name modification or
          // if you're wanting to rename it

          if ($_GET['update'] != "1"){
            
            // If $_GET['update'] is 1, this means you have clicked "Update Page Name", and the modification will be submitted.
            // If it is not 1, you will be shown the form below

            echo "<h2 style='font-size:14px;'>Rename <i>$title</i></h2>";
            echo "
                   <form action='?view=admin&do=pages&rename=$title&update=1' method='post'>
                     New Title<br />
                     <input type='text' name='title' size='30'><br /><br />
                     <input type='submit' value='Update Page Name'>
                   </form>
                 ";
                 
            // The form is now finished, we'll run the "else" code, and below will be the rename function.

          } else {
            
            /*************
            | We're now going to rename your page...
            | To do this, we must first check if the title is blank or not,
            | as we cannot have a blank page name now can we! (Rhetorical question for those confused..)
            *************/

            if (trim($_POST['title']) == null){

              // You have left the title blank (null, 0) so an error must be displayed
              echo "Please don't leave the title field blank.";

            } else {
              
              /*************
              | The title field was not blank, and now we can rename the page!
              | After renaming, you'll be redirected back to the page list.
              *************/

              // Set the new page title
              $new_title = $_POST['title'];

              // Rename the page
              rename("tpl/$title.html","tpl/$new_title.html");
              
              // Echo the refresh code to refresh the user back to the pages list
              echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&renamed=1\">";
              
              // Exit so this page cannot be left with content on it
              exit();

            } // end check if the title field was null
            
          } // end check if user is trying to save the renamed page or set a new name
          
        } else {
          
          // We did a check earlier to see if the file existed or not...
          // If it doesn't, we'll show this error message.
          echo "The file you're trying to rename doesn't exist.";

        } // end check if file exists
  
      } // end the rename script

    } else {
      
      /*************
      | The next codes are for deleting a page.
      | It has the same kind of structure as the rename function.
      *************/

      if ($_GET['delete'] != null){  // If $_GET['delete'] is not blank
        $title = $_GET['delete']; // Get the page title to delete
        
        /*************
        | Once again, we're not going to delete the home-page (or rename it like above)...
        | For this reason, we check if you're trying to delete it, if you are an error will be shown.
        *************/

        if ($title == "home"){ // If the title is "home"

          // Show an error, this is not allowed!
          echo "Sorry, you cannot delete the home page.";

        } else {

          /*************
          | So the file name is not home, we will now
          | continue with other checks.
          *************/

            if (file_exists("tpl/$title.html") || (file_exists("drafts/$title.html"))) { // Check if the file exists or not
            
            if ($_GET['draft'] == "1"){
              $m_draft = "&draft=1";
            }

            /*************
            | To prevent deleting pages by mistake, we'll have a confirm feature.
            | If you have not confirmed the deletion process, you will be prompted to do so.
            *************/

            if ($_GET['confirm'] != "1") { // If confirm is NOT 1 (yes)

              // Show the confirmation message
              echo "Are you sure you wish to delete <strong>$title</strong>? - <a href=\"?view=admin&do=pages&delete=$title&confirm=1$m_draft\">Yes</a>";

            } else {

                /*************
                | You have confirmed that you'd like to remove this page.
                | We can now do so by using the unlink() feature of PHP, which will delete
                | the file from your server.
                **************
                | If an error occurs with deleting the file using unlink,
                | you'll be promoted to manually remove the file. If this happens,
                | please check the permissions of the tpl folder.
                *************/

                // Unlink the file from the tpl/drafts folder where the file name is $title
                if ($_GET['draft'] == "1"){
                  unlink("drafts/$title.html") or die("Error, couldn't delete the file, if you wish to delete this, delete it manually from the drafts folder.");
                } else {
                  unlink("tpl/$title.html") or die("Error, couldn't delete the file, if you wish to delete this, delete it manually from the tpl folder.");
                }

                // Show a success message!
                echo "<div id=\"success\">Page deleted.</div>";

              } // end check confirmation

            } else {
              
              // If the page doesn't exist, we'll show an error
              echo "The page you're trying to delete doesn't exist.";

            } // end check if page exists
        } // end delete function

      } else {
        
        /*************
        | The next feature is used to create pages!
        | When adding a new page, this script is used.
        *************/

        if ($_GET['create'] == "new") { // If you're trying to create a new page
        
          /*************
          | Below we'll check if you're trying to save the page. If you are, we'll
          | run the "save" script. Else, we'll show the create a page form.
          *************/

          if ($_GET['save'] == "1") { // If you're trying to save the page
            
            // $_POST the content - title / content
            $title = $_POST['title']; // The page title
            $content = $_POST['page']; // The page content
            $draft = $_POST['draft'];

            /*************
            | Below we'll check if the page already exists or not.
            | If it does, you'll be redirected to the page and shown an error message.
            *************/

            if (file_exists("tpl/$title.html")) { // Check if the file with the title of $title exists
            
              // Redirects you back to the edit page with an error
              echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&edit=$title&updated=3\">";

              // Exit so this page cannot be left with content on it
              exit();

            } else {
              
            /*************
            | The file does not exist, so we can now write to a new file.
            **************
            | If an error occurs whilst creating a file,
            | If this happens, please check the permissions of the tpl folder.
            *************/
            
            /*************
            | TinyCMS 1.4 brings in the new "draft" feature so that you can
            | save pages without publishing them.
            *************/
            
            // Below we'll check if the page should be saved as a draft or not
            if ($draft == "1"){

              // Save the page in our drafts folder
              $handle = @fopen("drafts/$title DRAFT.html", "w") or die("Error, please check the permissions of your drafts folder.");

            } else {
              
              // Publish the page
              $handle = @fopen("tpl/$title.html", "w") or die("Error, please check the permissions of your tpl folder.");
              
            }

            // Write the new content to $handle (the file) and fill it with the $content
            fwrite($handle, $content);
            
            // Close $handle (the file) as we're finished with editing it
            fclose($handle);
            
            if ($draft == "1"){
              $draft_u = " DRAFT";
            }

            // Redirects you back to the page with a success message
            echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&edit=$title$draft_u&updated=2&draft=$draft\">";
            
            // Exit so this page cannot be left with content on it
            exit();

            } // end check if file exists already

          } else {

            /*************
            | If you have not submitted the page, you'll be shown the form.
            | You can create a page title and create the page content here.
            *************/

            echo "<form action=\"?view=admin&do=pages&create=new&save=1\" method=\"post\">";
            echo "<strong>Page Title</strong><br /><input type=\"text\" name=\"title\" size=\"50\"><br /><br />";
            echo "<textarea id=\"elm1\" name=\"page\"></textarea><br />";
            echo "<input type=\"radio\" name=\"draft\" value=\"1\" /> Save as draft<br />";
            echo "<input type=\"radio\" name=\"draft\" value=\"0\" /> Publish to site<br /><br />";
            echo "<input type=\"submit\" value=\"Save Page\">";
            echo "</form>";

          } // end check if you wish to save the file or not

        } else {
          
          /*************
          | This next feature is very similar to our create new page feature,
          | however of course instead of creating, we re-write the file (edit)
          *************/

          if ($_GET['edit'] != null) { // If you're trying to edit a page is not blank
          
            /*************
            | Below we'll check if you're trying to save the page. If you are, we'll
            | run the "save" script. Else, we'll show the create a page form.
            *************/

            if ($_GET['update'] != null) { // If update is not null (should be 1)

              /*************
              | We can now run the update script. We'll
              | pass the required variables (just 1).
              *************/

              // $file is our page title
              $file = $_GET['edit'];

              // Check if file is a draft or not
              if ($_GET['draft'] == "1"){

                // Check to publish draft
                if ($_POST['pub'] == "1"){
                  // Remove the draft & publish to live site
                  
                  // Create the file handle
                  $handle = @fopen("tpl/$file.html", "w") or exit("Error (<a href='?view=admin'>Admin Home</a>)");
                  
                  // The $posted_content is our new content
                  $posted_content = trim($_POST['page']); // Trim the content so it cannot be left blank
                  
                  fwrite($handle, $posted_content);
                  fclose($handle);

                  // Delete the draft file
                  unlink("drafts/$file.html");
                  
                  # -> Now redirect back to the edit page with the new file handle
                  echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&edit=$file&updated=1\">";
                  exit();

                } else {

                  // Save as draft (again)
                  $handle = @fopen("drafts/$file.html", "w") or exit("Error (<a href='?view=admin'>Admin Home</a>)");
                  
                }


              } else {

                // Our handle opens the file, if it doesn't exist an error will be shown and the script will be stopped.
                $handle = @fopen("tpl/$file.html", "w") or exit("Error, the page doesn't exist, the script has stopped. (<a href='?view=admin'>Admin Home</a>)");
                
              } // end check draft

              // The $posted_content is our new content
              $posted_content = trim($_POST['page']); // Trim the content so it cannot be left blank


              if ($_POST['page'] == null){

                // If the content is left blank, we'll redirect you back to the edit page with an error
                exit("<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&edit=$file&updated=0&draft=$_GET[draft]\">");

              } else {

                // Everything looks good, we can now update the $handle (file) with the new $posted_content
                fwrite($handle, $posted_content);
                
                // We've finished editing the page, so we can close the file
                fclose($handle);

                // If all was successful, you'll be redirected to the edit page with a success message
                echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=pages&edit=$file&updated=1&draft=$_GET[draft]\">";

              } // end check if the content was blank

            } else {
              
              /*************
              | If you have not submitted the edits, this page will be shown.
              *************/

              // Get the file you're trying to edit
              $file = $_GET['edit'];
              
              // Check if file is a draft or not
              if ($_GET['draft'] == "1"){

                // Our handle opens the file, if it doesn't exist an error will be shown and the script will be stopped.
                $handle = @fopen("drafts/$file.html", "r") or exit("Error, the draft doesn't exist, the script has stopped. (<a href='?view=admin'>Admin Home</a>)");

              } else {

                // Our handle opens the file, if it doesn't exist an error will be shown and the script will be stopped.
                $handle = @fopen("tpl/$file.html", "r") or exit("Error, the page doesn't exist, the script has stopped. (<a href='?view=admin'>Admin Home</a>)");

              } // end check draft

              // Below is our successful update message
              if ($_GET['updated'] == "1") {
                if ($_GET['draft'] == "1"){
                  echo "<div id=\"success\">Draft successfully updated</div>";
                } else {
                  echo "<div id=\"success\">Page successfully updated - <a href=\"../#$file\" target=\"_blank\">Check it out!</a></div>";
                }

              // Below is our unsuccessful update message
              } elseif ($_GET['updated'] == "0") {
                echo "<div id=\"error\">Page could not be updated</div>";

              // Below is our page created message
              } elseif ($_GET['updated'] == "2") {
                if ($_GET['draft'] == "1"){
                  echo "<div id=\"success\">Draft successfully created</div>";
                } else {
                  echo "<div id=\"success\">Page successfully created</div>";
                }

              // Below is our unsuccessful update message, page exists already
              } elseif ($_GET['updated'] == "3") {
                echo "<div id=\"error\">Page already exists</div>";
              }

              // Below is the form to update existing pages
              
              if ($_GET['draft'] == "1"){
                $draft = "checked";
              } elseif ($_GET['draft'] != "1"){
                $pub = "checked";
              }

              echo "<form action=\"?view=admin&do=pages&edit=$file&update=$file&draft=$_GET[draft]\" method=\"post\">";
              echo "<textarea id=\"elm1\" name=\"page\">" . fread($handle, 100000) . "</textarea><br />";
              echo "<input type=\"radio\" name=\"pub\" value=\"0\" $draft /> Save as draft<br />";
              echo "<input type=\"radio\" name=\"pub\" value=\"1\" $pub /> Publish to site<br /><br />";
              echo "<input type=\"submit\" value=\"Save Page\">";
              echo "</form>";
              
              // Close the file, we've opened it and no longer need the file to remain open
              fclose($handle);

            } // end check if update is null

          } else {
            
            // Successfully renamed a page message
            if ($_GET['renamed'] == "1"){
              echo "<div id=\"success\">Page successfully renamed.</div>";
            }


            // Below we'll list the pages and show edit options beside them
            
            // List pub files
            echo "<h3 style='color:#404040; margin-top:0px;'>Public Pages</h3>";

            // Below is our modified page listing with no remove/rename option showing for the home-page
            echo "<div class=\"listing\"><a href=\"?view=admin&do=pages&edit=home\" class=\"active\">Home</a>
                  <span style=\"float:right;\"><a href=\"?view=admin&do=pages&edit=home\"><img src=\"ico/edit.png\"></a></span>
                  </div>
                 ";

            if ($handle = opendir('tpl')) {

              while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {

                  $file = substr($file, 0, -(strlen($file)-(strrpos($file, "."))));
                  
                    // Check if the file name is WSL or HOME, we do not want these listed
                    if (($file == "wsl") || ($file == "home")){ } else {

                    // List all pages with edit/rename/delete options
                    echo "<div class=\"listing\"><a href=\"?view=admin&do=pages&edit=$file\" class=\"active\">" . ucfirst($file) . "</a>
                            <span style=\"float:right;\">
                              <a href=\"?view=admin&do=pages&edit=$file\"><img src=\"ico/edit.png\"></a>
                              <a href=\"?view=admin&do=pages&rename=$file\"><img src=\"ico/rename.png\"></a>
                              <a href=\"?view=admin&do=pages&delete=$file\"><img src=\"ico/delete.png\"></a>
                            </span>
                          </div>
                         ";
                    }

                }

              }

              // We've finished with our directory handle, and it can now be closed
              closedir($handle);

            }
            
            // List drafts
            echo "<h3 style='color:#404040;'>Draft Pages</h3>";
            
            if ($handle = opendir('drafts')) {

              while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {

                  $file = substr($file, 0, -(strlen($file)-(strrpos($file, "."))));
                  
                    // Check if the file name is WSL or HOME, we do not want these listed
                    if (($file == "wsl") || ($file == "home")){ } else {

                    // List all pages with edit/rename/delete options
                    echo "<div class=\"listing\"><a href=\"?view=admin&do=pages&edit=$file\" class=\"active\">" . ucfirst($file) . "</a>
                            <span style=\"float:right;\">
                              <a href=\"?view=admin&do=pages&edit=$file&draft=1\"><img src=\"ico/edit.png\"></a>
                              <a href=\"?view=admin&do=pages&delete=$file&draft=1\"><img src=\"ico/delete.png\"></a>
                            </span>
                          </div>
                         ";
                    }

                }

              }

              // We've finished with our directory handle, and it can now be closed
              closedir($handle);

            }

          }
        }
      }
    }
  }

  /*************
  | The final script in this file is our settings.
  | The following will allow us to update our custom_conf.php in inc/
  *************/

  function showSettings(){
    
    // If you're trying to update the settings
    if ($_GET['update'] == "1"){
      
      // Open the custom_conf file for writing
      $handle = @fopen("inc/custom_conf.php", "w") or exit("Error loading custom configuration, the script has stopped. (<a href='?view=admin'>Admin Home</a>)");
      
      // The new content to add to the file
      $posted_content = stripslashes($_POST[page]);
      
      // Write to $handle with $posted_content, we'll use the @ sign before the frwite
      // to prevent any errors showing, we'll use the DIE error instead!
      @fwrite($handle, $posted_content) or die("Permission error, please check the file and folder permissions of inc and custom_conf.php");

      // We're finished with $handle and can now close it
      fclose($handle);

      // If successful, redirect the user back to the settings page with a success message
      echo "<meta http-equiv=\"REFRESH\" content=\"0;url=?view=admin&do=settings&updated=1\">";

    } else {
      
      // Show a success message if updated is 1
      if ($_GET['updated'] == "1"){
        
        // Show the success message
        echo "<div id=\"success\">Custom configuration successfully updated</div>";
        
      } // end success message
      
      // $handle will open custom_conf so we can read it (r)
      $handle = @fopen("inc/custom_conf.php", "r") or exit("Error loading custom configuration, the script has stopped. (<a href='?view=admin'>Admin Home</a>)");
      
      // Show the settings form.
      echo "<form action=\"?view=admin&do=settings&update=1\" method=\"post\">";
      echo "<textarea name=\"page\">" . fread($handle, 100000) . "</textarea><br />";
      echo "<input type=\"submit\" value=\"Save Settings\">";
      echo "</form>";

      // We're finished with $handle and can now close it.
      fclose($handle);

    } // end check if you want to update the file
  } // end the settings function

?>