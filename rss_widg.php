<style>
    .rss_content {
        padding: 10px;
        background: #F3F3F3;
    }
    .rss_content_long {
        padding: 10px;
        background: #F3F3F3;
    }
    .rss_content_long:after {
        content: "\25BC";
        color: #4675AB;
        margin: 0 auto;
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
        border-top: none;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    }
    td.title_td {
        padding: 10px;
        padding-bottom: 0px; 
        padding-top: 0px;
        border-bottom: none;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    }
    td.gap_td {
        border:none;
    }
    span.date {
        color: #999999;
    }
    .more_button {
        color: #FFF;
        background-color: #4675AB;
        border: 1px solid #3868B6;
        -webkit-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.25);
        -moz-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.25);
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.25);
        margin: 0px;
        margin-right: 10px;
        text-align: center;
        text-transform: capitalize;
        padding-left: 14px;
        padding-right: 14px;
        border-radius: 3px;
        box-sizing: content-box;
        position: relative;
        float: right;
        display: none;
        cursor: pointer;
    }
    
    .rss_content_long:hover > a > .more_button {
        display: inline;
    }
    
    .rss_content:hover > a > .more_button {
        display: inline;
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
        echo '<table class="tableau" style="border: none; margin: 0px; ';
        $width = $atts['width'];
        if ($width != 'full') {
            if (strpos($width,'%')) {
                echo 'max-width: '.$width.';';
            } elseif (strpos($width,'px')) {
                echo 'max-width: '.$width.';';
            } else {
                echo 'max-width: '.$atts['width'].'px;';
            }
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
            $ext_len = $atts['max_chars'] - $prev_len;

            // if chars is set to full, make the preview the size of all of the content
            if ($atts['chars'] == 'full') {
                $prev_len = $content_len;
            }
            
            // create the preview and end substrings
            $content_prev = substr($content, 0, $prev_len);
            $content_end = substr($content, $prev_len, $ext_len);
            // if max_chars is full, then make the end the rest of the length
            if ($atts['max_chars'] == 'full') {
                $content_end = substr($content, $prev_len);
            }

            // start echoing to the embed with h5 title sizes, linked
            if ($title) {
                echo '<tr><td class="title_td">';
                echo '<h5><a href="'.$link.'">';
                echo $title.'</a></h5>';
                echo '</td></tr>';
            }

            // TESTING
            /*echo "length: ".$content_len."<br />";
            echo "prev len: ".$prev_len."<br />";
            echo "ext len: ".$ext_len."<br />";
            echo "allowed chars greater than content: ".($prev_len < $content_len)."<br />";
            echo "rolls is on: ".($atts['roll'] == 'yes')."<br />";
            echo "chars isn't set to full: ".($atts['chars'] != "full")."<br />";
            echo "the extension is greater than zero: ".($ext_len > 0)."<br />";*/
            // TESTING

            if ($content) {
                echo '<tr><td class="rss_td">';
                if ($prev_len < $content_len && $atts['roll'] == 'yes' && $atts['chars'] != "full" && $ext_len > 0) {
                    echo '<div class="rss_content_long">'.$content_prev;
                    echo '<span class="rss_content_ext">'.$content_end.'</span>';
                } else {
                   echo '<div class="rss_content">'.$content_prev;
                }
                echo "<br /><span class='date'>"." ".$date."</span>";
                echo "<a href='".$link."'><button class='more_button'>more</button></a><br /></div>";
                echo '</td></tr><tr><td class="gap_td">';
                echo '</td></tr>';
            }

        }
        echo '</table>';
    }

    $output_string = ob_get_contents();;
    ob_end_clean();

    return $output_string;

}
?>