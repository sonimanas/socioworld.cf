<?php
//error_reporting(0);
@ini_set("memory_limit", "-1");
@set_time_limit(0);
$ServerErrors = array();
$config_file_name = '../config.php';
function getBaseUrl() {
    $currentPath = $_SERVER['PHP_SELF'];
    $pathInfo = pathinfo($currentPath);
    $hostName = $_SERVER['HTTP_HOST'];
    return $hostName.$pathInfo['dirname'];
}
function check_($check) {
	return array('status'=>'SUCCESS');
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://askmescript.com/purchase.php?code=' . $check . '&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check             = json_decode($file, true);
    } else {
        $check             = array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
    }
    return $check;
}
function check_success($check) {
	return array('status'=>'SUCCESS');
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://askmescript.com/purchase.php?code=' . $check . '&success=true&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check             = json_decode($file, true);
    } else {
        $check             = array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
    }
    return $check;
}

if (!empty($_POST['install'])) {
   $con = mysqli_connect($_POST['sql_host'], $_POST['sql_user'], $_POST['sql_pass'], $_POST['sql_name']);
   if (mysqli_connect_errno()) {
       $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   if ($con) {
    /*
      $sql = mysqli_query($con, "SELECT @@sql_mode as modes;");
      $sql_sql = mysqli_fetch_assoc($sql);
      if (count($sql_sql) > 0) {
         $results = @explode(',', $sql_sql['modes']);
         if (in_array('STRICT_TRANS_TABLES', $results)) {
           $ServerErrors[] = "The sql-mode <b>STRICT_TRANS_TABLES</b> is enabled in your mysql server, please contact your host provider to disable it.";
         }
         if (in_array('STRICT_ALL_TABLES', $results)) {
           $ServerErrors[] = "The sql-mode <b>STRICT_ALL_TABLES</b> is enabled in your mysql server, please contact your host provider to disable it.";
         }
      }
    */
   }
   if (!filter_var($_POST['site_url'], FILTER_VALIDATE_URL)) {
       $ServerErrors[] = "Invalid site url";
   }
   if (empty($_POST['admin_username']) || empty($_POST['admin_password'])) {
       $ServerErrors[] = "Please provide right admin username/password";
   }
  $p = check_(trim($_POST['purshase_code']));
  if (isset($p['status'])) {
     if ($p['status'] == 'ERROR') {
       $ServerErrors[] = $p['ERROR_NAME'];
     }
  } else {
    $ServerErrors[] = 'Failed to connect to server, please try again later, or contact us.';
  }
   if (empty($ServerErrors)) {

    if (strpos($_POST['site_url'], '/',strlen($_POST['site_url']) - 1)) {
        $_POST['site_url'] =  substr($_POST['site_url'], 0,strlen($_POST['site_url']) - 1);
    }
      $file_content =
'<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.askmescript.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com
// +------------------------------------------------------------------------+
// | AskMe - The Ultimate PHP Question & Answers Platform
// | Copyright (c) 2019 AskMe. All rights reserved.
// +------------------------------------------------------------------------+
// MySQL Hostname
$sql_db_host = "'  . $_POST['sql_host'] . '";
// MySQL Database User
$sql_db_user = "'  . $_POST['sql_user'] . '";
// MySQL Database Password
$sql_db_pass = "'  . $_POST['sql_pass'] . '";
// MySQL Database Name
$sql_db_name = "'  . $_POST['sql_name'] . '";

// Site URL
$site_url = "' . $_POST['site_url'] . '"; // e.g (http://example.com)

$app = "AskMe";

// Purchase code
$purchase_code = "' . trim($_POST['purshase_code']) . '"; // Your purchase code, don\'t give it to anyone.
?>';
$success = '';
$config_file = file_put_contents($config_file_name, $file_content);
    if ($config_file) {
        $filename = '../ask.sql';
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line) {
           // Skip it if it's a comment
           if (substr($line, 0, 2) == '--' || $line == '')
              continue;
           // Add this line to the current segment
           $templine .= $line;
           $query = false;
           // If it has a semicolon at the end, it's the end of the query
           if (substr(trim($line), -1, 1) == ';') {
              // Perform the query
              $query = mysqli_query($con, $templine);
              // Reset temp variable to empty
              $templine = '';
           }
        }
        if ($query) {
			mysqli_close($con);
            $p2 = check_success(trim($_POST['purshase_code']));
            if(isset($p2['status'])) {
                if ($p2['status'] == 'SUCCESS') {
                  $can = 1;
                }
            }
            $con2 = mysqli_connect($_POST['sql_host'], $_POST['sql_user'], $_POST['sql_pass'], $_POST['sql_name']);
            $query_one  = mysqli_query($con2, "UPDATE `config` SET `value` = '" . mysqli_real_escape_string($con2, $_POST['siteName']). "' WHERE `name` = 'name'");
            $query_one .= mysqli_query($con2, "UPDATE `config` SET `value` = '" . mysqli_real_escape_string($con2, $_POST['siteTitle']). "' WHERE `name` = 'title'");
            $query_one .= mysqli_query($con2, "UPDATE `config` SET `value` = '" . mysqli_real_escape_string($con2, $_POST['siteEmail']). "' WHERE `name` = 'email'");
            $query_one .= mysqli_query($con2, "INSERT INTO `users` (`username`,`password`, `email`, `admin`, `active`, `verified`, `last_active`, `registered`, `startup`,`user_upload_limit`) VALUES ('" . mysqli_real_escape_string($con2, $_POST['admin_username']). "', '" . mysqli_real_escape_string($con2, sha1($_POST['admin_password']) ) . "','" . mysqli_real_escape_string($con2, $_POST['siteEmail']) . "','1', '1', '1', '".time()."','".date('Y') . '/' . intval(date('m'))."', '2','1000000');");

            $success = 'AskMe successfully installed, please wait ..';
			
			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			  exit;
			}

			mysqli_close($con2);
        } else {
            $ServerErrors[] = "Error found while installing, please contact us.";
        }
      }
   }
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>AskMe | Installation</title>
        <link rel="shortcut icon" type="image/png" href="icon.png"/>
        <link rel="stylesheet" href="font-awesome.min.css">
        <link rel="stylesheet" href="bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript" src="jquery-1.11.3.js"></script>
        <script type="text/javascript" src="init.js"></script>
        <script type="text/javascript" src="jquery.form.min.js"></script>
    </head>
    <body>
        <?php 
            $page = 'terms';
            $pages_array = array(
               'req',
               'terms',
               'installation',
               'finish'
            );
            if (!empty($_GET['page'])) {
               if (in_array($_GET['page'], $pages_array)) {
                  $page = $_GET['page'];
               }
            }
            $page_icon = '';
            $page_name = '';
            if ($page == 'terms') {
            	$page_name = 'Terms of use';
                $page_icon = 'bars';
            } else if ($page == 'req') {
            	$page_name = 'Requirements';
                $page_icon = 'cog';
            } else if ($page == 'installation') {
            	$page_name = 'Installation';
                $page_icon = 'download';
            }else if ($page == 'finish') {
            	$page_name = 'Finish';
                $page_icon = 'check';
            }
            $cURL = true;
            $php = true;
            $gd = true;
            $disabled = false;
            $mysqli = true;
            $is_writable = true;
            $mbstring = true;
            $is_htaccess = true;
            $is_mod_rewrite = true;
            $is_sql = true;
            $zip = true;
            $allow_url_fopen = true;
            $exif_read_data = true;
            if (!function_exists('curl_init')) {
                $cURL = false;
                $disabled = true;
            }
            if (!function_exists('mysqli_connect')) {
                $mysqli = false;
                $disabled = true;
            }
            if (!extension_loaded('mbstring')) {
                $mbstring = false;
                $disabled = true;
            }
            if (!extension_loaded('gd') && !function_exists('gd_info')) {
                $gd = false;
                $disabled = true;
            }
            if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
                $php = false;
                $disabled = true;
            }
            if (!is_writable('../config.php')) {
                $is_writable = false;
                $disabled = true;
            }
            if (!file_exists('../.htaccess')) {
                $is_htaccess = false;
                $disabled = true;
            }
            if (!file_exists('../ask.sql')) {
                $is_sql = false;
                $disabled = true;
            }
            if (!extension_loaded('zip')) {
                $zip = false;
                $disabled = true;
            }
            if(!ini_get('allow_url_fopen') ) {
                $allow_url_fopen = false;
                $disabled = true;
            }
?>
        <div class="content-container container">
                <div class="row admin-panel">
                    <div class="col-md-3">
                        <ul class="list-group">
                            <li class="list-group-item black-list <?php echo ($page == 'terms') ? 'active-list': '';?>"><i class="fa fa-fw fa-bars"></i> Terms of use</li>
                            <li class="list-group-item black-list <?php echo ($page == 'req') ? 'active-list': '';?>"><i class="fa fa-fw fa-cog"></i> Requirements</li>
                            <li class="list-group-item black-list <?php echo ($page == 'installation') ? 'active-list': '';?>"><i class="fa fa-fw fa-download"></i> Installation</li>
                            <li class="list-group-item black-list <?php echo ($page == 'finish') ? 'active-list': '';?>"><i class="fa fa-fw fa-check"></i> Finish</li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="list-group">
                            <div class="list-group-item"><i class="fa fa-fw fa-<?php echo $page_icon?>"></i> <?php echo $page_name?></div>
                            <div class="setting-well">
                                <?php if ($page == 'terms') { ?>
                                <div class="terms">
                                    <h5>LICENSE AGREEMENT: one (1) Domain (site) Install</h5>
                                    <br>
                                    <b>You CAN:</b><br> 1) Use on one (1) domain only, additional license purchase required for each additional domain.<br> 2) Modify or edit as you see fit.<br> 3) Delete sections as you see fit.<br> 4) Translate to your choice of language.<br>
                                    <br><b>You CANNOT:</b> <br>1) Resell, distribute, give away or trade by any means to any third party or individual without permission.<br> 2) Use on more than one (1) domain.
                                    <br><br>Unlimited Licenses are available.
                                    <hr>
                                    <form action="?page=req" method="post">
                                        <div class="form-group">
                                            <input type="checkbox" id="agree" name="agree"> I agree to the terms of use and privacy policy
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-2 pull-left last-btn">
                                                <button type="submit" class="btn btn-main" id="next-terms" disabled>
                                                <i class="fa fa-arrow-right progress-icon" data-icon="paper-plane-o"></i> Next
                                                </button>
                                            </div>
                                            <div class="setting-saved-update-alert milinglist"></div>
                                        </div>
                                    </form>
                                </div>
                                <?php } else if ($page == 'req') { ?>
                                <div class="req">
                                     <table class="table table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>PHP 5.5+</td>
        <td>Required PHP version 5.5 or more</td>
        <td><?php echo ($php == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>cURL</td>
        <td>Required cURL PHP extension</td>
        <td><?php echo ($cURL == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>MySQLi</td>
        <td>Required MySQLi PHP extension</td>
        <td><?php echo ($mysqli == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>GD Library</td>
        <td>Required GD Library for image cropping</td>
        <td><?php echo ($gd == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>Mbstring</td>
        <td>Required Mbstring extension for UTF-8 Strings</td>
        <td><?php echo ($mbstring == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>ZIP</td>
        <td>Required ZIP extension for backuping data</td>
        <td><?php echo ($zip == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
      </tr>
      <tr>
        <td>allow_url_fopen</td>
        <td>Required allow_url_fopen</td>
        <td><?php echo ($allow_url_fopen == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Enabled</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Disabled</font>'?></td>
      </tr>
      <tr>
        <td>.htaccess</td>
        <td>Required .htaccess file for script security <small>(Located in ./Script)</small></td>
        <td><?php echo ($is_htaccess == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Uploaded</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not uploaded</font>'?></td>
      </tr>
      <tr>
        <td>ask.sql</td>
        <td>Required ask.sql for the installation <small>(Located in ./Script)</small></td>
        <td><?php echo ($is_sql == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Uploaded</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not uploaded</font>'?></td>
      </tr>
      <tr>
        <td>config.php</td>
        <td>Required config.php to be writable for the installation</td>
        <td><?php echo ($is_writable == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Writable</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not writable</font>'?></td>
      </tr>
    </tbody>
  </table>
                                    
                                    <br>
                                    <form action="?page=installation" method="post">
                                        <div class="row">
                                            <div class="col-sm-2 pull-left last-btn">
                                                <button type="submit" class="btn btn-main" id="next-terms" <?php echo ($disabled == true) ? 'disabled': '';?>>
                                                <i class="fa fa-arrow-right progress-icon" data-icon="paper-plane-o"></i> Next
                                                </button>
                                            </div>
                                            <div class="setting-saved-update-alert milinglist"></div>
                                        </div>
                                    </form>
                                </div>
                                <?php } else if ($page == 'finish') { ?>
                                <div class="req">
                                    <h5>AskMe successfully installed, if you have any question, please let us <a href="mailto:wowondersocial@gmail.com">know</a></h5>
                                    <br>
                                    <h5><a href="../">Let's Start !</a></h5>
                                </div>
                                <?php } else if ($page == 'installation') { ?>
                                <div class="req">
                                   <?php
                                   if (!empty($ServerErrors)) {
                                   ?>
                                   <div class="alert alert-danger">
                                    <?php
                                       foreach ($ServerErrors as  $value) {
                                           echo '- ' . $value . "<br>";
                                       }
                                    ?>
                                   </div>
                                   <?php } else if (!empty($success)) { ?>
                                   <div class="alert alert-success">
                                    <i class="fa fa-check"></i> <?php echo $success;?>
                                    <script type="text/javascript">
                                    var URL = '?page=finish';
                                    var delay = 1000; //Your delay in milliseconds
                                    setTimeout(function(){ window.location = URL; }, delay);
                                    </script>
                                   </div>
                                   <?php } ?>
                                    <form action="?page=installation" method="post" class="form-horizontal install-site-setting">
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteName">Purchase code </label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="purshase_code" value="<?php echo (isset($_POST['purshase_code'])) ? trim($_POST['purshase_code']): '';?>">
                                                <span class="help-block">Enter random value</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteName">SQL host name </label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sql_host" value="<?php echo (!empty($_POST['sql_host'])) ? $_POST['sql_host']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteTitle">SQL username</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sql_user" value="<?php echo (!empty($_POST['sql_user'])) ? $_POST['sql_user']: '';?>"> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteName">SQL password </label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sql_pass" value="<?php echo (!empty($_POST['sql_pass'])) ? $_POST['sql_pass']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteTitle">SQL database name</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sql_name" value="<?php echo (!empty($_POST['sql_name'])) ? $_POST['sql_name']: '';?>"> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="site_url">Site url</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="site_url" value="<?php echo (!empty($_POST['site_url'])) ? $_POST['site_url']: '';?>">
                                                <span class="help-block">Examples: <br>http://siteurl.com<br> http://www.siteurl.com<br> http://subdomain.siteurl.com<br> http://siteurl.com/subfolder<br> You can use https:// too.</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail">Site name</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="siteName" value="<?php echo (!empty($_POST['siteName'])) ? $_POST['siteName']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail">Site title</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="siteTitle" value="<?php echo (!empty($_POST['siteTitle'])) ? $_POST['siteTitle']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail">Site E-mail</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="siteEmail" value="<?php echo (!empty($_POST['siteEmail'])) ? $_POST['siteEmail']: '';?>">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail">Admin username</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="admin_username" value="<?php echo (!empty($_POST['admin_username'])) ? $_POST['admin_username']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail">Admin passowrd</label>  
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="admin_password" value="<?php echo (!empty($_POST['admin_password'])) ? $_POST['admin_password']: '';?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3" for="siteEmail"></label>  
                                            <div class="col-md-6">
                                                Note: Installation process may take few minutes.
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="install" value="install">
                                        <div class="form-group last-btn">
                                           <label class="col-md-3"></label>  
                                           <div class="col-md-6">
                                              <button type="submit" onclick="SubmitButton();" class="btn btn-main" <?php echo ($disabled == true) ? 'disabled': '';?>>
                                              <i class="fa fa-download progress-icon" data-icon="download"></i> Install
                                              </button>   
                                           </div>
                                        </div>
                                    </form>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </body>
</html>
<style>
    button:disabled {
        color: #fff !important;
    }
</style>
<script>
function SubmitButton() {
    $('button').attr('disabled', true);
    $('button').text('Please wait..');
    $('form').submit();
}
    $(function() {
        $('#agree').change(function() {
            if($(this).is(":checked")) {
                $('#next-terms').attr('disabled', false);
            } else {
            	$('#next-terms').attr('disabled', true);
            }       
        });
    });
</script>
<style>
    body {
    background: #f9f9f9;
    }
    form {
    margin-bottom: 0;
    }
    .btn-main {
    color: #ffffff;
    background-color: #0ca678;
    border-color:#0ca678;
    }
    .btn-main:disabled {
    color: #333;
    border: none;
    }
    .btn-main:hover {
    color: #ffffff;
    background-color: #0ca678;
    border-color:#0ca678;
    }
    .admin-panel .col-md-9 .list-group-item:first-child,
    .setting-panel .col-md-8 .list-group-item:first-child,
    .profile-lists .list-group-item:first-child,
    .col-md-8 .list-group-item:first-child,
    .col-sm-4 .list-group-item:first-child,
    .red-list .list-group-item:first-child {
    color: #ffffff;
    background-color:#0ca678;
    }
    .admin-panel .col-md-9 .list-group-item:first-child a,
    .setting-panel .col-md-8 .list-group-item:first-child a,
    .profile-lists .list-group-item:first-child a,
    .col-md-8 .list-group-item:first-child a {
    color: #ffffff !important;
    }
    .list-group-item.black-list.active-list {
    color: #ffffff;
    background-color: #0ca678;
    }
    .list-group-item.black-list {
    background: #ffffff;
    }
    .profile-top-line {
    background-color: #0ca678;
    }
    #bar {
    background-color: #0ca678;
    }
    .list-group-item.black-list a {
    color: #444444;
    }
    .list-group-item.black-list.active-list a {
    color: #ffffff;
    }
    .main-color,
    .small-text a {
    color: #0ca678 !important;
    }
    .search-advanced-container a:hover {
    text-decoration: none;
    color: #ffffff;
    background-color: #0ca678;
    }
    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus,
    .nav-tabs>li.active>a:hover {
    color: #ffffff;
    cursor: default;
    color: #0ca678;
    border-bottom: 1px solid #0ca678;
    background-color: transparent
    }
    .btn-active {
    color: #ffffff;
    background: #0ca678;
    outline: none;
    border: 1px solid #0ca678;
    }
    .btn-active:hover,
    .btn-active:focus {
    border: 1px solid #0ca678;
    color: #ffffff;
    background: #0ca678;
    }
    .btn-active-color:hover {
    background: #0ca678;
    }
    .chat-container .online-toggle {
    background: #0ca678;
    }
    .chat-tab .online-toggle {
    background: #0ca678;
    }
    .profile-style .user-follow-button button.btn-active,
    .btn-login,
    .btn-register {
    background: #0ca678;
    color: #ffffff;
    }
    .profile-style .user-follow-button button.btn-active:hover,
    .btn-login:hover,
    .btn-login:focus,
    .btn-register:hover,
    .btn-register:focus {
    color: #ffffff;
    background: #0ca678;
    }
    .panel-login button:disabled {
    background-color: #08a62a;
    }
    .panel-login>.panel-heading a.active {
    color: #0ca678;
    font-size: 18px;
    }
    table, td, th, tr {
      font-size: 14px !important; 
    }
    small {
      color: #555 !important;
    }
</style>