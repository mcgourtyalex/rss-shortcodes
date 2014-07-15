<style>
    .rss_content {
        padding: 10px;
        background: #EFEFEF;
    }
    .rss_content_long {
        padding: 10px;
        background: #EFEFEF;
    }
    .rss_content_long:after {
        content: " > more";
        font-weight: bold;
    }
    .rss_content_long:hover:after {
        content: "";
    }
    .rss_content_ext {
        display: none;
    }
    .rss_content_long:hover > span {
        display: inline;
    }
    td.rss_td {
        padding: 0px;
    }
</style>
<?php
/*
Plugin Name: RSS Widget
Description: Embeds RSS in pages and posts with shortcodes
*/

add_shortcode( 'rss_embed', 'rss_embed_content' );
function rss_embed_content($atts) {
    $atts = shortcode_atts( array(
        'href' => '#',
        'number' => 5,
        'chars' => 250,
        'max_chars' => 500,
        'roll' => 'yes',
        'width' => 500,
    ), $atts );

    $rss = fetch_feed($atts['href']);

    if (!is_wp_error($rss)) {
        $rss_items = $rss->get_items(0,$atts['number']);
        echo '<table style="border:none;';
        if ($atts['width'] != 'full') {
            echo 'max-width: '.$atts['width'].'px;';
        }
        echo '">';

        $count = 1;

        foreach($rss_items as $rss_item) {
            $title = $rss_item->get_title();
            $link = $rss_item->get_link();
            $date = $rss_item->get_date();

            $content = trim(strip_tags($rss_item->get_description()));
            $content_len = strlen($content);

            $prev_len = $atts['chars'];
            $ext_len = $atts['max_chars'] - $prev_len;

            if ($atts['chars'] == 'full') {
                $prev_len = $content_len;
                $ext_len = 0;
            }
            
            $content_prev = substr($content,0,$prev_len);
            $content_end = substr($content, $prev_len, $ext_len);
            if ($atts['max_chars'] == 'full') {
                $content_end = substr($content,$prev_len);
            }

            echo '<tr><td style="padding-bottom: 0px; padding-top: 0px;">';
            echo '<h5><a href="'.$link.'">'.$title.'</a></h5>';
            echo '</td></tr><tr><td class="rss_td">';
            if ($content_len > $content_prev && $atts['roll'] == 'yes' && $atts['chars'] != "full") {
                echo '<div class="rss_content_long">'.$content_prev;
                echo '<span class="rss_content_ext">'.$content_end.'</span>';
            } else {
               echo '<div class="rss_content">'.$content_prev;
            }
            echo "<strong>"." ".$date."</strong><br /></div>";
            echo '</td></tr><tr><td style="border:none;">';
            echo '</td></tr>';

            if ($count == $atts['number']) {
                break;
            }
            $count++;
        }
        echo '</table>';
    }
}
?>