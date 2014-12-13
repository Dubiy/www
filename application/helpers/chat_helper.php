<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('video_url_parse_helper')) {
    function video_url_parse_helper($link = '') {
            $video_info = array('error' => 'error');

            switch ($link) {

                //WTF?! Чому воно працює?)

                // https://www.youtube.com/watch?v=KnT-r7FeOY4#t=1
                case strpos($link, 'youtu.be/') !== FALSE: 
                case strpos($link, 'youtube.com/') !== FALSE: {
                    $video_code = '';

                    $link = explode('#', $link);
                    $link = $link[0];

                    $parts = explode('?', trim($link));
                    if (count($parts) > 1) {
                        $parts = explode('&', $parts[1]);
                        foreach ($parts as $part) {
                            $block = explode('=', $part);
                            if ($block[0] == 'v') {
                                $video_code = $block[1];
                            }
                        }
                    } else {
                        $parts = explode('youtu.be/', trim($link));
                        if (isset($parts[1])) {
                            $video_code = $parts[1];
                        } else {
                            $video_code = '';
                        }
                    }

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://www.youtube.com/get_video_info?video_id=' . $video_code);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $links = curl_exec($ch);
                    curl_close($ch);
                    parse_str($links, $info);

                    $video_info = array(
                        'iframe_src' => "//www.youtube.com/embed/$video_code",
                        'thumbnail_url' => "//img.youtube.com/vi/$video_code/1.jpg",
                        'title' => @$info['title'],
                        'length_seconds' => '00:00'
                    );
                } break;
                case strpos($link, 'mail.ru/') !== FALSE: {
                    //http://my.mail.ru/mail/ar1na/video/700/1475.html
                    //http://content.video.mail.ru/mail/ar1na/700/i-1475.jpg
                    
                    //http://api.video.mail.ru/videos/embed/mail/ar1na/700/1475.html
                    $user_name = '';
                    $video_code1 = '';
                    $video_code2 = '';
                    switch ($link) {
                        case strpos($link, 'my.mail.ru/mail') !== FALSE: {
                            //http://my.mail.ru/mail/mehdi.vafa/video/668/735.html
                            $exploded = explode('my.mail.ru/mail', $link);
                            $exploded = explode('/', $exploded[1]);
                            if (is_array($exploded) && count($exploded) >= 5) {
                                $user_name = $exploded[1];
                                $video_code1 = $exploded[3];
                                $video_code2 = $exploded[4];
                                $video_code2 = explode('.', $exploded[4]);
                                $video_code2 = $video_code2[0];                            
                            }
                        } break;
                        case strpos($link, 'api.video.mail.ru/videos') !== FALSE: {
                            //http://api.video.mail.ru/videos/embed/mail/ar1na/700/1475.html
                            $exploded = explode('api.video.mail.ru/videos', $link);
                            $exploded = explode('/', $exploded[1]);
                            if (is_array($exploded) && count($exploded) >= 6) {
                                $user_name = $exploded[3];
                                $video_code1 = $exploded[4];
                                $video_code2 = explode('.', $exploded[5]);
                                $video_code2 = $video_code2[0];
                            }
                        } break;
                        default: {


                            echo 'wrong url type';
                            return;
                        }
                    }
                    if ($user_name == '' || $video_code1 == '' || $video_code2 == '') {
                        return $video_info;
                        return;
                    } 

                    $video_info = array(
                            'iframe_src' => "//api.video.mail.ru/videos/embed/mail/$user_name/$video_code1/$video_code2.html",
                            'thumbnail_url' => "//content.video.mail.ru/mail/$user_name/$video_code1/i-$video_code2.jpg",
                            'title' => '',
                            'length_seconds' => '00:00'
                        );

                } break;

                case strpos($link, 'rutube.ru/video/') !== FALSE: {
                    // http://rutube.ru/video/3386afecee6c369065fa17aa91c2ba01/






                    $exploded = explode('rutube.ru/video', $link);
                    $exploded = explode('/', $exploded[1]);
                    $video_code = $exploded[1];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://rutube.ru/api/video/' . $video_code . '/?format=xml');
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $info = curl_exec($ch);
                    curl_close($ch);
                    
                    $p = xml_parser_create();
                    xml_parse_into_struct($p, $info, $vals);
                    xml_parser_free($p);
                    
                    $iframe_src = 'http://rutube.ru/video/embed/' . $video_code;
                    $thumbnail_url = '';
                    $title = '';
                    foreach ($vals as $val) {
                        if (strtoupper($val['tag']) == 'THUMBNAIL_URL') {
                            $thumbnail_url = $val['value'];
                        }
                        if (strtoupper($val['tag']) == 'TITLE') {
                            $title = $val['value'];
                        }
                    }

                    //http://rutube.ru/api/video/90439f0088b2d5e2bb210ad08fa835a1/?format=xml


                    $video_info = array(
                            'iframe_src' => $iframe_src,
                            'thumbnail_url' => $thumbnail_url,
                            'title' => $title,
                            'length_seconds' => '00:00'
                        );

                } break;
                case strpos($link, 'vimeo.com/') !== FALSE: {
                    //http://vimeo.com/18875180
                    $exploded = explode('vimeo.com', $link);
                    $exploded = explode('/', $exploded[1]);
                    $video_code = $exploded[1];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'http://vimeo.com/api/v2/video/' . $video_code . '.xml');
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $info = curl_exec($ch);
                    curl_close($ch);
                    
                    $p = xml_parser_create();
                    xml_parse_into_struct($p, $info, $vals);
                    xml_parser_free($p);

                    $thumbnail_url = '';
                    $title = '';
                    foreach ($vals as $val) {
                        if (strtoupper($val['tag']) == 'THUMBNAIL_MEDIUM') {
                            $thumbnail_url = $val['value'];
                        }
                        if (strtoupper($val['tag']) == 'TITLE') {
                            $title = $val['value'];
                        }
                    }

                    $video_info = array(
                            'iframe_src' => "//player.vimeo.com/video/$video_code",
                            'thumbnail_url' => $thumbnail_url,
                            'title' => $title,
                            'length_seconds' => '00:00'
                        );

                } break;
            }
            return $video_info;
        }



    if (!function_exists('filter_post_text')) {
        function filter_post_text($text = '') {
            global $CI;
            global $matched_users;
            $CI = & get_instance();
            $CI->load->model('Attachment_model', '', TRUE);


            function replace_user_link_function($match = '') {
              global $matched_users;
              if (isset($match[1]) && $matched_users[$match[1]]) {
                return '<div class="user_link" user_id="' . $match[1] . '">' . $matched_users[$match[1]] . '</div>';
              } else {
                return ' ';
              }
            }

            function filter_attachment_function($match = '') {
                global $CI;
                $attachment_id = $match[2];
                $attachment = $CI->Attachment_model->get($attachment_id);
                if (is_array($attachment) && count($attachment)) {
                    $res = '<div class="attachment" attachment_id="' . $attachment[0]->attachment_id . '">';
                    switch ($attachment[0]->type) {
                        case 'photo':
                            $res .= '<img src="/' . $CI->config->item('photos_upload_path') . $attachment[0]->path . '" title="' . htmlspecialchars($attachment[0]->comment) . '">';
                            break;
                        case 'video':
                            $res .= '<iframe src="' . $attachment[0]->path . '" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>';
                            break;
                        default:
                            $res .= 'PLEASE ADD HANDLER IN FILE: ' . __FILE__ . ":" . __LINE__;
                            break;
                    }
                    return $res . '</div>';
                } 
                return '';
            }

            function filter_link_href_function($match = '') {
              if (strpos($match[2], 'http') === 0) {
                return '<a href="' . $match[2] . '" target="_blank">' . $match[4] . '</a>';
              } else {
                return '';
              }
            }

            function replace_text_link_function($match = '') {
              return (($match[0][0] = ' ') ? (' ') : ('')) . '<a href="' . trim($match[0]) . '" target="_blank">' . trim($match[0]) . '</a>';
            }

            $text = preg_replace('/&nbsp;/is', " ", $text);
            $text = str_replace("</div>", "</div>\n", $text);
            $text = strip_tags($text, '<b><u><strike><i><p><a><img><user>');
            $user_link_mask = '/<user uid=\"(.*?)\">(.*?)<\/user>/is';
            preg_match_all($user_link_mask, $text, $matched_users);
            $matched_users_tmp = $CI->User_model->get_by_ids($matched_users[1]);
            $matched_users = array();
            if (is_array($matched_users_tmp) && count($matched_users_tmp)) {
                foreach ($matched_users_tmp as $item) {
                  $matched_users[$item->user_id] = $item->nickname;
                }
                $text = preg_replace_callback ($user_link_mask, 'replace_user_link_function', $text);
            }
            $text = preg_replace('/<img class=\"attachment(.*?)\"(.*?)>/is', '<yyyimg$2>', $text); // zzzimg щоб <i(.*?)> не захавало <img>
            // echo '<hr><hr><hr>' . htmlspecialchars($text) . '<hr><hr><hr>';

            $text = preg_replace('/<img(.*?)>/is', '<zzzimg$1>', $text); // zzzimg щоб <i(.*?)> не захавало <img>
            $text = preg_replace('/<b(.*?)>(.*?)<\/b>/is', '<b>$2</b>', $text);
            $text = preg_replace('/<i(.*?)>(.*?)<\/i>/is', '<i>$2</i>', $text);
            $text = preg_replace('/<u(.*?)>(.*?)<\/u>/is', '<u>$2</u>', $text);
            $text = preg_replace('/<strike(.*?)>(.*?)<\/strike>/is', '<strike>$2</strike>', $text);
            $text = preg_replace('/<p(.*?)>(.*?)<\/p>/is', '<p>$2</p>', $text);
            $text = preg_replace('/<p><\/p>/is', '', $text);
            $text = preg_replace('/<zzzimg(.*?)emoji u(.*?)\"(.*?)>/is', '<img src="/0.png" class="emoji u$2">', $text);

            // $text = preg_replace_callback ('/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/is', 'filter_link_href_function', $text);
            $text = preg_replace_callback('/<yyyimg(.*?)attachment_id=\"(.*?)\"(.*?)>/is', 'filter_attachment_function', $text);
            
            $text = preg_replace('/<zzzimg(.*?)src="(.*?)"(.*?)>/is', '<img src="$2">', $text);
            $text = preg_replace_callback ('/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/is', 'filter_link_href_function', $text);

            $regex = '$(^|\s)\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i';
            // preg_match_all($regex, $string, $result, PREG_PATTERN_ORDER);
            //$text = preg_replace($regex, '<a href="    ___KILLSPACESBITCH____    $0" target="_blank">    ___KILLSPACESBITCH____    $0</a>', $text);
            //$text = preg_replace('/(\s*)___KILLSPACESBITCH____(\s*)/is', '', $text);
            //replace_text_link_function

            $text = preg_replace_callback ($regex, 'replace_text_link_function', $text);

            // $text = preg_replace('/<imgsmile(.*?)emoji u(.*?)\"(.*?)>/is', '<img src="/0.png" class="emoji u$2">', $text);

            return nl2br($text);
        }
    }
}















if (!function_exists('is_crawler')) {
    function is_crawler() {
        // return true;
        $bots = array(
            'Googlebot', 'Baiduspider', 'ia_archiver',
            'R6_FeedFetcher', 'NetcraftSurveyAgent', 'Sogou web spider',
            'bingbot', 'Yahoo! Slurp', 'facebookexternalhit', 'PrintfulBot',
            'msnbot', 'Twitterbot', 'UnwindFetchor',
            'urlresolver', 'Butterfly', 'TweetmemeBot', 'Yandex');

        foreach($bots as $b){
            if (stripos($_SERVER['HTTP_USER_AGENT'], $b) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('rating_likes')) {
    function rating_likes($rating = 0) {
        return '+' . $rating;
    }
}

if (!function_exists('rating_dislikes')) {
    function rating_dislikes($rating = 0) {
        return (($rating == 0) ? ('-0') : ($rating));
    }
}

if (!function_exists('rating')) {
  function rating($rating = 0) {
    $sign = (($rating < 0) ? ('-') : (''));
    $rating = abs($rating);
    $start_rating = $rating;
    $chunks = array(
        //  http://orteil.dashnet.org/cookieclicker/
        array(1000000000000000, 'Q'), // ;)
        array(1000000000000, 'T'), // ;)
        array(1000000000, 'B'), // ;)
        array(1000000, 'M'),
        array(1000, 'K'),
        array(1, '')
    );
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $rating = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($start_rating / $rating)) != 0) {
            break;
        }
    }
    $print = $count . $name;
    return $print;
  }
}

if (!function_exists('create_thumb')) {
    function create_thumb($path = '', $filename = '', $new_path = '', $new_filename = '', $thumb_width = 100, $thumb_height = 100, $no_crop = false, $startX = -1, $startY = -1, $source_width = -1, $source_height = -1) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch (strtolower($ext)) {
            case 'png': {
                    $img = @imagecreatefrompng($path . $filename);
                } break;

            case 'jpg': {
                    $img = @imagecreatefromjpeg($path . $filename);
                } break;

            case 'gif': {
                    $img = @imagecreatefromgif($path . $filename);
                } break;
        }

        if ( ! $img) {
            //something wrong
            @unlink($path . $filename);
            return FALSE;
        }

        $img_width = imagesx($img);
        $img_height = imagesy($img);


        if ($startX != -1 && $startY != -1 && $source_width != -1 && $source_height != -1) {
            $new_img = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($new_img, $img, 0, 0, $startX, $startY, $thumb_width, $thumb_height, $source_width, $source_height);
        } elseif ($no_crop) {
            //// NO_CROP. Результат матиме такі ж пропорції як і оригінал, але не перевищуватиме розімірів
            if ($img_width < $thumb_width && $img_height < $thumb_height) {
                $new_width = $img_width;
                $new_height = $img_height;
            } else {
                if ($img_width / $thumb_width < $img_height / $thumb_height) {
                    $koef = $thumb_height / $img_height;
                } else { 
                    $koef = $thumb_width / $img_width;
                }
                $new_width = $img_width * $koef;
                $new_height = $img_height * $koef;
            }
            $new_img = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height);
        } else {
            //// CROP. Результат буде саме таких розмірів. Якщо пропорції оригіналу і результату різні, то зайві краї обріжуться.
            if ($img_width / $thumb_width > $img_height / $thumb_height) {
                $koef = $img_height / $thumb_height;
            } else {
                $koef = $img_width / $thumb_width;
            }

            $new_width = $img_width / $koef;
            $new_height = $img_height / $koef;

            $offsetX = 0;
            $offsetY = 0;
            if ($new_width > $thumb_width) {
                $offsetX = floor(($new_width - $thumb_width) * $koef / 2);
            }
            if ($new_height > $thumb_height) {
                $offsetY = floor(($new_height - $thumb_height) * $koef / 2);
            }

            $new_img = imagecreatetruecolor($thumb_width, $thumb_height);
            $backgroundColor = imagecolorallocate($new_img, 255, 255, 255);
            imagefill($new_img, 0, 0, $backgroundColor);
            imagecopyresampled($new_img, $img, 0, 0, $offsetX, $offsetY, $new_width, $new_height, $img_width, $img_height);
        }

        $ext = pathinfo($new_filename, PATHINFO_EXTENSION);
        switch (strtolower($ext)) {
            case 'png': {
                    imagepng($new_img, $new_path . $new_filename);
                } break;

            case 'jpg': {
                    imagejpeg($new_img, $new_path . $new_filename, 100);
                } break;

            case 'gif': {
                    imagegif($new_img, $new_path . $new_filename);
                } break;
        }
        return TRUE;
    }

}