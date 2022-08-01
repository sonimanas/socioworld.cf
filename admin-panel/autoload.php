<?php 

$page = 'dashboard';
if (!empty($_GET['page'])) {
    $page = Secure($_GET['page']);
}


$page_loaded = '';
$pages = array(
	'social-login',
    'dashboard',
    'general-settings', 
    'site-settings', 
    'email-settings', 
    's3',
    'prosys-settings',
    'manage-payments',
    'payment-requests', 
    'manage-users', 
    'manage-videos', 
    'import-from-youtube', 
    'import-from-dailymotion',
    'import-from-twitch', 
    'manage-video-ads', 
    'create-video-ad', 
    'edit-video-ad', 
    'manage-website-ads', 
    'manage-user-ads',
    'manage-themes', 
    'change-site-desgin', 
    'create-new-sitemap', 
    'manage-pages',
    'add-new-custom-page',
    'edit-custom-page',
    'manage-custom-pages', 
    'changelog',
    'backup',
	'bank-receipts',
    'create-article',
    'edit-article',
    'manage-articles',
    'manage-profile-fields',
    'add-new-profile-field',
    'edit-profile-field',
    'payment-settings',
    'verification-requests',
    'manage-announcements',
    'ban-users',
    'custom-design',
    'api-settings',
    'manage-video-reports',
    'manage-languages',
    'add-language',
    'edit-lang',
    'sell_videos',
    'manage_categories',
    'push-notifications-system',
    'fake-users',
    'auto-friend',
    'manage-questions',
    'manage-answers',
    'manage-reply',
    'edit-question',
    'ads-earning',
    'manage-reports',
    'manage-invitation-keys',
    'send_email',
    'mailing-list',
    'backups',
    'mass-notifications',
    'backup' 
);

if (in_array($page, $pages)) {
    $page_loaded = LoadAdminPage("$page/content");
} 

if (empty($page_loaded)) {
    header("Location: " . UrlLink('admincp'));
    exit();
}

if ($page == 'dashboard') {
    if ($ask->config->last_admin_collection < (time() - 1800)) {
        $update_information = UpdateAdminDetails();
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Admin Panel | <?php echo $ask->config->title; ?></title>
    <link rel="icon" href="<?php echo $ask->config->theme_url ?>/img/icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <script src="<?php echo LoadAdminLink('plugins/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo LoadAdminLink('plugins/bootstrap/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo LoadAdminLink('plugins/node-waves/waves.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/animate-css/animate.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/morrisjs/morris.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/bootstrap-tagsinput/src/bootstrap-tagsinput.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('css/style.css'); ?>" rel="stylesheet">
    <link href="<?php echo LoadAdminLink('plugins/sweetalert/sweetalert.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css'); ?>" rel="stylesheet">
    <link href="<?php echo LoadAdminLink('plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo LoadAdminLink('css/themes/all-themes.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/bootstrap-select/css/bootstrap-select.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/sweetalert/sweetalert.css'); ?>" rel="stylesheet" />
    <link href="<?php echo LoadAdminLink('plugins/m-popup/magnific-popup.css'); ?>" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo $ask->config->theme_url; ?>/js/jquery.form.min.js"></script>
    <link href="<?php echo $ask->config->theme_url; ?>/css/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" />

</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="<?php echo UrlLink(''); ?>"><?php echo $ask->config->title ?></a>
            </div>
             <div class="navbar-header pull-right">
                <div class="form-group form-float pt_admin_hdr_srch">
                    <div class="form-line">
                        <input type="text" id="search_for" name="search_for" class="form-control" onkeyup="searchInFiles($(this).val())" placeholder="Search Settings">
                    </div>
                    <div class="pt_admin_hdr_srch_reslts" id="search_for_bar"></div>
                </div>
            </div>
        </div>

    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <img src="<?php echo $user->avatar; ?>" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name"><a href="<?php echo $user->url; ?>" target="_blank"><?php echo $user->name; ?></a></div>
                    <div class="email"><?php echo $user->email; ?></div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li <?php echo ($page == 'dashboard') ? 'class="active"' : ''; ?>>
                        <a href="<?php echo LoadAdminLinkSettings(''); ?>">
                            <i class="material-icons">dashboard</i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li <?php echo ($page == 'general-settings' || $page == 'site-settings' || $page == 'payment-settings' || $page == 'email-settings' || $page == 'social-login' || $page == 's3') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">settings</i>
                            <span>Settings</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'general-settings') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('general-settings'); ?>">General Settings</a>
                            </li>
                            <li <?php echo ($page == 'site-settings') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('site-settings'); ?>">Site Settings</a>
                            </li>
                            <li <?php echo ($page == 'email-settings') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('email-settings'); ?>">E-mail & SMS Settings</a>
                            </li>
                           <li <?php echo ($page == 'social-login') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('social-login'); ?>">Social Login Settings</a> 
                            </li>
                            <li <?php echo ($page == 's3') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('s3'); ?>">Amazon S3 & FTP Settings</a>
                            </li>

                            <li <?php echo ($page == 'payment-settings') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('payment-settings'); ?>">Payment Settings</a>
                            </li> 
							    
                        </ul>
                    </li>
                    <li <?php echo ($page == 'manage-languages' || $page == 'add-language' || $page == 'edit-lang') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">language</i>
                            <span>Languages</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'add-language') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('add-language'); ?>">Add New Language & Keys</a>
                            </li>
                            <li <?php echo ($page == 'manage-languages') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-languages'); ?>">Manage Languages</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php echo ($page == 'manage-users'  || $page == 'manage-reports' || $page == 'edit-profile-field' || $page == 'verification-requests') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">account_circle</i>
                            <span>Users</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'manage-users') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-users'); ?>">Manage Users</a>
                            </li>
                            <li <?php echo ($page == 'verification-requests') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('verification-requests'); ?>">Manage Verification Requests</a>
                            </li>
                            <li <?php echo ($page == 'manage-reports') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-reports'); ?>">Manage Reports</a>
                            </li>
                        </ul>
                        
                    </li>
                    <li <?php echo ($page == 'manage-questions' || $page == 'manage-answers' || $page == 'manage-reply' || $page == 'edit-question' ) ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">help</i>
                            <span>Questions</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'manage-questions' || $page == 'edit-question' ) ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-questions'); ?>">Manage Questions</a>
                            </li>
                            <li <?php echo ($page == 'manage-answers' || $page == 'manage-reply') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-answers'); ?>">Manage Answers</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php echo ($page == 'ads-earning' || $page == 'manage-video-ads' || $page == 'create-video-ad' || $page == 'edit-video-ad' || $page == 'payment-requests' || $page == 'manage-website-ads' || $page == 'manage-user-ads') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">attach_money</i>
                            <span>Advertisement</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'manage-website-ads') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-website-ads'); ?>">Manage Website Ads</a>
                            </li>
                            <li <?php echo ($page == 'manage-user-ads') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-user-ads'); ?>">Manage User Ads</a>
                            </li>
                            <li <?php echo ($page == 'ads-earning') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('ads-earning'); ?>">Earnings</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php echo ($page == 'manage-themes' || $page == 'change-site-desgin' || $page == 'custom-design') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">color_lens</i>
                            <span>Design</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo ($page == 'manage-themes') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-themes'); ?>">Themes</a>
                            </li>
                            <li <?php echo ($page == 'change-site-desgin') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('change-site-desgin'); ?>">Change Site Design</a>
                            </li>
                            <li <?php echo ($page == 'custom-design') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('custom-design'); ?>">Custom Design</a>
                            </li>
                        </ul>
                    </li>
					    <li <?php echo ($page == 'bank-receipts') ? 'class="active"' : ''; ?>>

                        <a href="<?php echo LoadAdminLinkSettings('bank-receipts'); ?>">

                            <i class="material-icons">credit_card</i>

                            <span>Manage Bank Receipts</span>

                        </a>

                    </li>
                    <li <?php echo ($page == 'manage-announcements' || $page == 'ban-users' || $page == 'fake-users' || $page == 'create-new-sitemap'  || $page == 'mailing-list' ||  $page == 'backups' || $page == 'mass-notifications' || $page == 'manage-invitation-keys' || $page == 'backup' || $page == 'auto-friend' || $page == 'send_email'  ) ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">build</i>
                            <span>Tools</span>
                        </a>
                        <ul class="ml-menu">
                            
                            <li <?php echo ($page == 'manage-invitation-keys') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-invitation-keys'); ?>">Invitation Codes</a>
                            </li>
                            <li <?php echo ($page == 'manage-announcements') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-announcements'); ?>">Manage Announcements</a>
                            </li>
                              <li <?php echo ($page == 'send_email') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('send_email'); ?>">Send E-mail</a>
                            </li>
                             <li <?php echo ($page == 'mailing-list') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('mailing-list'); ?>">Mailing List</a>
                            </li>
                            <li <?php echo ($page == 'mass-notifications') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('mass-notifications'); ?>">Mass Notifications</a>
                            </li>
                            
                            <li <?php echo ($page == 'auto-friend') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('auto-friend'); ?>">Auto Follow</a>
                            </li>
                            <li <?php echo ($page == 'ban-users') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('ban-users'); ?>">Ban Users</a>
                            </li>
                            <li <?php echo ($page == 'fake-users') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('fake-users'); ?>">Fake User Generator</a>
                            </li>
                            <li <?php echo ($page == 'create-new-sitemap') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('create-new-sitemap'); ?>">Create Sitemap</a>
                            </li>
                            <li <?php echo ($page == 'backup') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('backup'); ?>">Backup SQL & Files</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php echo ($page == 'manage-pages' || $page == 'add-new-custom-page') ? 'class="active"' : ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">description</i>
                            <span>Pages</span>
                        </a>
                        <ul class="ml-menu">
                             <li <?php echo ($page == 'manage-custom-pages' || $page == 'add-new-custom-page' || $page == 'edit-custom-page') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-custom-pages'); ?>">Manage Custom Pages</a>
                            </li>
                            <li <?php echo ($page == 'manage-pages') ? 'class="active"' : ''; ?>>
                                <a href="<?php echo LoadAdminLinkSettings('manage-pages'); ?>">Manage Pages</a>
                            </li>
                        </ul>
                    </li>
                    <li <?php echo ($page == 'changelog') ? 'class="active"' : ''; ?>>
                        <a href="<?php echo LoadAdminLinkSettings('changelog'); ?>">
                            <i class="material-icons">update</i>
                            <span>Changelogs</span>
                        </a>
                    </li>
                     <li >
                        <a href="http://docs.askmescript.com" target="_blank">
                            <i class="material-icons">more_vert</i>
                            <span>FAQs & Docs</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    Copyright &copy; <?php  echo date('Y') ?> <a href="javascript:void(0);"><?php echo $ask->config->name; ?></a>.
                </div>
                <div class="version">
                    <b>Version: </b> <?php echo $ask->config->version ?>
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
    </section>

    <section class="content">
        <?php echo $page_loaded; ?>
    </section>
    
    <!-- Bootstrap Core Js -->
    <script src="<?php echo LoadAdminLink('plugins/bootstrap/js/bootstrap.js'); ?>"></script>

    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/jquery.dataTables.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/buttons.flash.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/jszip.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/pdfmake.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/vfs_fonts.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/buttons.html5.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/jquery-datatable/extensions/export/buttons.print.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('js/pages/tables/jquery-datatable.js'); ?>"></script>

    <!-- Select Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/bootstrap-select/js/bootstrap-select.js'); ?>"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/jquery-slimscroll/jquery.slimscroll.js'); ?>"></script>

    <!-- ColorPicker Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js'); ?>"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/node-waves/waves.js'); ?>"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/jquery-countto/jquery.countTo.js'); ?>"></script>

    <!-- Morris Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/raphael/raphael.min.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/morrisjs/morris.js'); ?>"></script>
    <!-- Sparkline Chart Plugin Js -->
    <script src="<?php echo LoadAdminLink('plugins/jquery-sparkline/jquery.sparkline.js'); ?>"></script>
    <!-- TinyMce Text Editor  -->
    <script src="<?php echo LoadAdminLink('plugins/tinymce/js/tinymce/tinymce.min.js'); ?>"></script>
    <!-- Bootstrap tagsinput Plugin Js  -->
    <script src="<?php echo LoadAdminLink('plugins/bootstrap-tagsinput/src/bootstrap-tagsinput.js'); ?>"></script>

    <!-- Jquery Alert Plugin Js-->
    <script src="<?php echo LoadAdminLink('plugins/sweetalert/sweetalert.min.js'); ?>"></script>

     <!-- Jquery Magnific Pop-up Plugin Js-->
    <script src="<?php echo LoadAdminLink('plugins/m-popup/jquery.magnific-popup.min.js'); ?>"></script>


    <script src="<?php echo LoadAdminLink('plugins/codemirror-5.30.0/lib/codemirror.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/codemirror-5.30.0/mode/css/css.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('plugins/codemirror-5.30.0/mode/javascript/javascript.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo LoadAdminLink('plugins/codemirror-5.30.0/lib/codemirror.css'); ?>">
    <!-- Custom Js -->
    <script src="<?php echo LoadAdminLink('js/admin.js'); ?>"></script>
    <script src="<?php echo LoadAdminLink('js/pages/index.js'); ?>"></script>
</body>

</html>
<style> 
.sidebar .user-info {
    background: #0ca678 !important;
}
.theme-red .sidebar .menu .list li.active > :first-child i, .theme-red .sidebar .menu .list li.active > :first-child span {
    color: #0ca678 !important;
}
.btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:focus{
    background-color: #0ca678 !important;
}
.theme-red .sidebar .legal .copyright a{
    color: #0ca678 !important;
}
[type="radio"]:checked + label:after, [type="radio"].with-gap:checked + label:before, [type="radio"].with-gap:checked + label:after {
    border: 2px solid #0ca678;
}
[type="radio"]:checked + label:after, [type="radio"].with-gap:checked + label:after {
    background-color: #0ca678;
    z-index: 0;
}
a {
    color: #0ca678;
    text-decoration: none;
}
.bg-cyan {
    background-color: #0ca678 !important;
    color: #fff;
}
.btn-info, .btn-info:hover, .btn-info:active, .btn-info:focus {
    background-color: #0ca678 !important;
}
.pagination li.active a {
    background-color: #0ca678;
}
[type="checkbox"]:checked + label:before {
    border-right: 2px solid #333;
}
</style>
<script>
<?php echo LoadAdminPage('js/main'); ?>

function searchInFiles(keyword) {
    if (keyword.length > 2) {
        $.post('<?php echo $ask->config->site_url; ?>/aj/ap/search_in_pages', {keyword: keyword}, function(data, textStatus, xhr) {
            if (data.html != '') {
                $('#search_for_bar').html(data.html)
            }
            else{
                $('#search_for_bar').html('')
            }
        });
    }
    else{
        $('#search_for_bar').html('')
    }
}
</script>
