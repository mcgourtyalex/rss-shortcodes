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

    // attribute defaults
    $atts = shortcode_atts( array(
        'href' => '#',
        'number' => 5,
        'chars' => 250,
        'max_chars' => 500,
        'roll' => 'yes',
        'width' => 500,
    ), $atts );

    // fetch feed using WP
    $rss = fetch_feed($atts['href']);

    if (!is_wp_error($rss)) {
        // start a buffer so echo can be used
        ob_start();
        echo '<br />';

        // get items
        $rss_items = $rss->get_items(0,$atts['number']);

        // start table and config width -- 'full' for full width
        echo '<table style="border: none; margin: 0px; ';
        if ($atts['width'] != 'full') {
            echo 'max-width: '.$atts['width'].'px;';
        }
        echo '">';

        // iterate over all items
        foreach($rss_items as $rss_item) {

            // vars
            $title = $rss_item->get_title();
            $link = $rss_item->get_link();
            $date = $rss_item->get_date();

            // trim & strip content
            $content = trim(strip_tags($rss_item->get_description()));
            $content_len = strlen($content);

            // prev_len is the length of the preview text
            $prev_len = $atts['chars'];

            // ext_len is the length of the rest
            $ext_len = $content_len - $prev_len;

            // if chars is set to full, 
            if ($atts['chars'] == 'full') {
                $prev_len = $content_len;
            }
            
            $content_prev = substr($content, 0, $prev_len);
            $content_end = substr($content, $prev_len, $ext_len);
            if ($atts['max_chars'] == 'full') {
                $content_end = substr($content, $prev_len);
            }

            echo '<tr><td style="padding-bottom: 0px; padding-top: 0px;">';
            echo '<h5><a href="'.$link.'">'.$title.'</a></h5>';
            echo '</td></tr><tr><td class="rss_td">';



            // TESTING
            /*echo "length: ".$content_len."<br />";
            echo "prev len: ".$prev_len."<br />";
            echo "ext len: ".$ext_len."<br />";
            echo "allowed chars greater than content: ".($prev_len < $content_len)."<br />";
            echo "rolls is on: ".($atts['roll'] == 'yes')."<br />";
            echo "chars isn't set to full: ".($atts['chars'] != "full")."<br />";
            echo "the extension is greater than zero: ".($ext_len > 0)."<br />";*/
            // TESTING

            if ($prev_len < $content_len && $atts['roll'] == 'yes' && $atts['chars'] != "full" && $ext_len > 0) {
                echo '<div class="rss_content_long">'.$content_prev;
                echo '<span class="rss_content_ext">'.$content_end.'</span>';
            } else {
               echo '<div class="rss_content">'.$content_prev;
            }
            echo "<strong>"." ".$date."</strong><br /></div>";
            echo '</td></tr><tr><td style="border:none;">';
            echo '</td></tr>';

        }
        echo '</table>';
    }

    $output_string = ob_get_contents();;
    ob_end_clean();


    return $output_string;

}
?>