<style>
    .rss_content {
        padding: 10px;
        background: #F3F3F3;
    }
    .rss_content_prev {
        padding: 10px;
        background: #F3F3F3;
    }
    .rss_content_prev:after {
        content: "\25BC";
        color: #4675AB;
        display: block;
        margin: 0 auto;
        position: relative;
        text-align: center;
    }
    .rss_content_prev:hover:after {
        content: "";
    }
    .rss_content_ext {
        display: none;
    }

    .ellipses {
        display: inline;
    }
    
    .rss_content_prev:hover > .ellipses:after {
        content: "";
    }

    .ellipses:after {
        content: "..."
    }
    
    .ellipses_ext {
        display: none;
    }
    
    .ellipses_ext:after {
        content: "...";
    }
    
    .rss_content_prev:hover > span {
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
    
    .rss_content_prev:hover > a > .more_button {
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

    $roll = $atts['roll'];

    if (!is_wp_error($rss)) {
        // start a buffer so echo can be used
        ob_start();
        echo '<br />';

        // get items
        $rss_items = $rss->get_items(0,$atts['number']);
        
        $width = $atts['width'];

        // start table and config width -- 'full' for full width
        echo '<table style="border: none; margin: 0px; ';
        width_handler($width);
        echo '">';

        // iterate over all items
        foreach($rss_items as $rss_item) {

            // vars
            $title = $rss_item->get_title();
            $link = $rss_item->get_link();
            $date = $rss_item->get_date();
            $content = $rss_item->get_description();

            // trim & strip content
            $content = content_trim($content);
            $content_len = strlen($content);

            // total_len is the total length of the prev and the ext together
            $total_len = $atts['max_chars'];

            // prev_len is the length of the preview text
            $prev_len = preview_length_handler($atts, $content_len);

            // ext_len is the length of the rest
            $ext_len = $total_len - $prev_len;


            
            // create the preview and end substrings
            $content_prev = substr($content, 0, $prev_len);

            $content_end = content_string_handler($content, $prev_len, $ext_len);

            // start echoing to the embed with h5 title sizes, linked
            if ($title) {
                echo '<tr><td class="title_td">';
                echo '<h5><a href="';
                echo $link;
                echo '">';
                echo $title;
                echo '</a></h5>';
                echo '</td></tr>';
            }

            // display content
            if ($content) {
                echo '<tr><td class="rss_td">';

                // test if there should be rolldown
                if (activate_roll ($prev_len, $content_len, $roll, $ext_len)) {
                    echo '<div class="rss_content_prev">';
                    echo $content_prev;
                    echo '<div class="ellipses"></div>';
                    echo '<span class="rss_content_ext';
                    if ($total_len < $content_len) {
                        echo ', ellipses_ext';
                    }
                    echo '">';
                    echo $content_end;
                    echo '</span>';
                } else {
                   echo '<div class="rss_content">'.$content_prev;
                }

                // add date
                echo "<br /><span class='date'>"." ".$date."</span>";
                // add button link
                echo "<a target='_blank' href='".$link."'><button class='more_button'>more</button></a><br /></div>";
                echo '</td></tr><tr><td class="gap_td"></td></tr>';
            }

        }
        echo '</table>';
    }

    $output_string = ob_get_contents();;
    ob_end_clean();

    return $output_string;

}

function width_handler($width) {
    if ($width != 'full') {
        if (strpos($width,'%')) {
            echo 'max-width: '.$width.';';
        } elseif (strpos($width,'px')) {
            echo 'max-width: '.$width.';';
        } else {
            echo 'max-width: '.$width.'px;';
        }
    }
}

function content_trim($content) {
    return trim(strip_tags($content));
}

function preview_length_handler($atts, $content_len) {
    $prev_len = $atts['chars'];
    // if chars is set to full, make the preview the size of all of the content
    if ($prev_len == 'full') {
        $prev_len = $content_len;
    }
    return $prev_len;
}

function content_string_handler($content, $prev_len, $ext_len) {
    $content_end = substr($content, $prev_len, $ext_len);
    // if max_chars is full, then make the end the rest of the length
    if ($total_len == 'full') {
        $content_end = substr($content, $prev_len);
    }
    return $content_end;
}

function activate_roll ($prev_len, $content_len, $roll, $ext_len) {
    return $prev_len != "full" && $prev_len < $content_len && $roll == 'yes' && $ext_len > 0;
}

?>