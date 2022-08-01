<?php
require_once('./assets/init.php');
$page = 'home';
if (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}

if (IS_LOGGED == true) {
    if ($user->last_active < (time() - 60)) {
        $update = $db->where('id', $user->id)->update('users', array(
            'last_active' => time()
        ));
    }
    if($page !== 'logout') {
        if ($user->startup == 0) {
            $page = 'steps';
            $step_type = 'avatar';
        } else if ($user->startup == 1) {
            $page = 'steps';
            $step_type = 'info';
        }
    }
}

if (file_exists("./sources/$page/content.php")) {
    include("./sources/$page/content.php");
}

if (empty($ask->content)) {
    include("./sources/404/content.php");
}

$data['title'] = $ask->title;
$data['description'] = $ask->description;
$data['keyword'] = $ask->keyword;
$data['page'] = $ask->page;
$data['url'] = $ask->page_url_;
?>
    <input type="hidden" id="json-data" value='<?php echo str_replace("'","&apos;", htmlspecialchars(json_encode($data)));?>'>
<?php
echo $ask->content;
$db->disconnect();
unset($ask);
?>