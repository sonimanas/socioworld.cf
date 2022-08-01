  <?php 
  if (IS_LOGGED == false ) {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}
  if ($first == 'posts') {

    if ($second == 'fetch_url') {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST["url"], $match)) {
            $youtube_video = Secure($match[1]);
            $api_request   = file_get_contents('https://www.googleapis.com/youtube/v3/videos?id=' . $youtube_video . '&key=AIzaSyDoOC41IwRzX5XvP7bNiCJXJfcK14HalM0&part=snippet,contentDetails,statistics,status');
            $thumbnail     = '';
            if (!empty($api_request)) {
                $json_decode = json_decode($api_request);
                if (!empty($json_decode->items[0]->snippet)) {
                    if (!empty($json_decode->items[0]->snippet->thumbnails->maxres->url)) {
                        $thumbnail = $json_decode->items[0]->snippet->thumbnails->maxres->url;
                    }
                    if (!empty($json_decode->items[0]->snippet->thumbnails->medium->url)) {
                        $thumbnail = $json_decode->items[0]->snippet->thumbnails->medium->url;
                    }
                    $info        = $json_decode->items[0]->snippet;
                    $title       = $info->title;
                    $description = $info->description;
                    if (!empty($json_decode->items[0]->snippet->tags)) {
                        if (is_array($json_decode->items[0]->snippet->tags)) {
                            foreach ($json_decode->items[0]->snippet->tags as $key => $tag) {
                                $tags_array[] = $tag;
                            }
                            $tags = implode(',', $tags_array);
                        }
                    }
                }
                $output = array(
                    'title' => $title,
                    'images' => array(
                        $thumbnail
                    ),
                    'content' => $description,
                    'url' => $_POST["url"]
                );
                
                echo json_encode($output);
                exit();
            }
        } else if (isset($_POST["url"])) {
            $page_title = '';
            $image_urls = array();
            $page_body  = '';
            $get_url    = $_POST["url"];
            
            include_once("assets/import/simple_html_dom.php");
            $get_image = getimagesize($get_url);
        
            if (!empty($get_image)) {
                $image_urls[] = $get_url;
                $page_title   = 'Image';
            } else {
                $get_content = file_get_html($get_url);
                if (!empty($get_content)) {
                    # code...
                
                foreach ($get_content->find('title') as $element) {
                    @$page_title = $element->plaintext;
                }
                if (empty($page_title)) {
                    $page_title = '';
                }
                @$page_body = $get_content->find("meta[name='description']", 0)->content;
                $page_body = mb_substr($page_body, 0, 250, "utf-8");
                if ($page_body === false) {
                    $page_body = '';
                }
                if (empty($page_body)) {
                    @$page_body = $get_content->find("meta[property='og:description']", 0)->content;
                    $page_body = mb_substr($page_body, 0, 250, "utf-8");
                    if ($page_body === false) {
                        $page_body = '';
                    }
                }
                $image_urls = array();
                @$page_image = $get_content->find("meta[property='og:image']", 0)->content;
                if (!empty($page_image)) {
                    if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $page_image)) {
                        $image_urls[] = $page_image;
                    }
                } else {
                    foreach ($get_content->find('img') as $element) {
                        if (!preg_match('/blank.(.*)/i', $element->src)) {
                            if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $element->src)) {
                                $image_urls[] = $element->src;
                            }
                        }
                    }
                }
            }
            }
            $output = array(
                'title' => $page_title,
                'images' => $image_urls,
                'content' => $page_body,
                'url' => $_POST["url"]
            );
       
            echo json_encode($output);
            exit();
        }
    }
}