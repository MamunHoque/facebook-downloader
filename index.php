<!DOCTYPE html>
    <html>
    <head>
        <title>Facebook Video Downloader</title>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.2/css/bulma.css">
    </head>
    <body>
        <div class="container" style="padding-top: 50px;">

            <form method="post">
                <div class="field has-addons has-addons-centered">
                    <p class="control">
                        <input class="input" type="text" name="url" placeholder="Paste Your Video URL Here">
                    </p>
                    <p class="control">
                        <span class="select">
                            <select name="video_quality">
                                <option value="hd" selected>HD</option>
                                <option value="sd">SD</option>
                            </select>
                        </span>
                    </p>
                    <p class="control">
                        <span class="select">
                            <select name="option">
                                <option value="direct" selected>Direct</option>
                                <option value="manual">Manual</option>
                            </select>
                        </span>
                    </p>
                    <p class="control">
                        <button class="button is-primary">Search</button>
                    </p>
                </div>

                <?php

                if (isset($_POST['url'])) {

                    $url = $_POST['url'];

                    $context = [
                        'http' => [
                            'method' => 'GET',
                            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36",
                        ],
                    ];
                    $context = stream_context_create($context);
                    $file_data = file_get_contents($url, false, $context);

                    $video_quality = isset($_POST['video_quality']) ? $_POST['video_quality'] : 'hd';
                    $regex = '/'. $video_quality . '_src_no_ratelimit:"([^"]+)"/';

                    if (preg_match($regex, $file_data, $match)) {
                        $video = $match[1];
                        $title = null;
                        if (preg_match('/h2 class="uiHeaderTitle"?[^>]+>(.+?)<\/h2>/', $file_data, $matches)) {
                            $title = $matches[1];
                        } elseif (preg_match('/title id="pageTitle">(.+?)<\/title>/', $file_data, $matches)) {
                            $title = $matches[1];
                        }

                        $title = html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8');

                        if(isset($_POST['option']) &&$_POST['option']=='direct' ){
                             header("Content-Disposition: attachment; filename='" . $title . ".mp4'");
                             readfile($video);
                             exit;
                        }

                        $img_link = explode("/", $url);
                        $image_url = "https://graph.facebook.com/{$img_link[6]}/picture";

                ?>
                <div class="field has-addons has-addons-centered">
                    <div class="box">
                        <article class="media">
                            <div class="media-left">
                                <figure class="image is-128x128">
                                    <img src="<?php echo $image_url; ?>" alt="Image" height="77">
                                </figure>
                            </div>
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong><?php echo $title; ?></strong>
                                    </p>
                                    <p class="control">
                                        <a class="button is-primary"  href="<?php echo $video ?>">Save as Download</a>
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                <?php
                    }

                }


                ?>

            </form>
        </div>
    </body>
</html>