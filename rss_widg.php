<!DOCTYPE html>

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
    display: block;
    margin: 0 auto;
    position: relative;
    text-align: center;
}

.rss_content_long:hover:after {
    content: "";
}
.rss_content_ext {
    display: none;
}

.ellipses {
    display: inline;
}
    
.rss_content_long:hover > .ellipses:after {
    content: "";
}

.ellipses:after {
    content: "...";
}
    
.ellipses_ext {
    display: none;
}
    
.ellipses_ext:after {
    content: "...";
}
    
.rss_content_long:hover > span {
    display: inline;
}
td.rss_td {
    padding: 0px;
    border-top: none;
    box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -moz-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -webkit-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    background: #F3F3F3;
}
td.title_td {
    padding: 10px;
    border-bottom: none;
    box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -moz-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
    -webkit-box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3);
}
td.gap_td {
    border:none;
    height: 10px;
    border-top: 1px solid #dddddd;
}
span.date {
    color: #999999;
}
    
div.author {
    color: #999999;
    display: inline;
    float: right;
}
    
a.author_link {
    color: #999999;
    text-decoration: none;
    text-decoration-color: none;
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
    margin-bottom: 10px;
    text-align: center;
    text-transform: capitalize;
    padding-left: 14px;
    padding-right: 14px;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
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

a.item_title {
    font-size: 16px;
    font-weight: bold;
    margin-left: 10px;
}

div.circle_fun {
    width: 5px;
    height: 5px;
    border-radius: 5px;
    border: 1px solid #999999;
    float: left;
    transition: all 0.4s ease-in-out;
    background: #F3F3F3;
}
    
a.item_title:hover > div.circle_fun {
    width: 10px;
    height: 10px;
    border-radius: 10px;
    border-bottom-right-radius: 0px;
    float: left;
    background: #F3F3F3;
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
    $atts = defaults($atts);

    // fetch feed using WP
    $rss = fetch_feed($atts['href']);

    $roll = $atts['roll'];
    $width = $atts['width'];
    // total_len is the total length of the prev and the ext together
    $total_len = $atts['max_chars'];
    // prev_len is the length of the preview text
    $prev_len = preview_length_handler($atts, $content_len);
    // ext_len is the length of the rest
    $ext_len = $total_len - $prev_len;

    if (!is_wp_error($rss)) {
        // start a buffer so echo can be used
        ob_start();

        echo '<br />';

        // get items
        $rss_items = $rss->get_items(0,$atts['number']);
        

        // start table and config width -- 'full' for full width
        echo '<table style="border: none; margin: 0px; ';
        width_handler($width);
        echo '">';

        // iterate over all items
        foreach($rss_items as $rss_item) {

            // vars
            $title = $rss_item->get_title();
            $title = title_trim($title);
            $link = $rss_item->get_link();
            $date = $rss_item->get_date();
            $author = $rss_item->get_author()->get_name();
            $author_link = $rss_item->get_author()->get_link();
            $category = $rss_item->get_category();
            // trim & strip content
            $content = $rss_item->get_description();
            $content = content_trim($content);
            $content_len = strlen($content);

            // create the preview and end substrings
            $content_prev = content_prev_string_handler($content, $prev_len);
            $content_end = content_end_string_handler($content, $prev_len, $ext_len);

            // start echoing to the embed with h5 title sizes, linked
            if ($title && $content) {
                echo '<tr><td class="title_td">';
                echo '<a href="';
                echo $link;
                echo '" class="item_title">';
                echo '<div class="circle_fun"></div>';
                echo strip_tags($title);
                echo '</a>';
                echo '<div class="author">';
                echo '<a class="author_link" ';
                if ($author_link) {
                    echo 'href='.$author_link;
                }
                echo '>';
                echo $author;
                echo '</a>';
                if ($category) {
                    echo " | ";
                    echo $category;
                }
                echo '</div>';
                echo '';
                echo '</td></tr>';

                echo '<tr><td class="rss_td">';

                // test if there should be rolldown
                if (activate_roll ($prev_len, $content_len, $roll, $ext_len)) {
                    echo '<div class="rss_content_long">';
                    echo $content_prev;
                    echo '<div class="ellipses"></div>';
                    echo '<span class="rss_content_ext';
                    extension_ellipses_handler ($total_len, $content_len);
                    echo '">';
                    echo $content_end;
                    echo '</span>';
                } else {
                   echo '<div class="rss_content">';
                   echo $content_prev;
                }

                // add date
                echo "<br /><span class='date'>"." ";
                echo $date;
                echo "</span>";
                // add button link
                echo "<a target='_blank' href='";
                echo $link;
                echo "'><button class='more_button'>more</button></a><br /></div>";
                echo '</td></tr>';
                // create gap between posts
                echo '<tr><td class="gap_td"> </td></tr>';
            }

        }
        echo '</table>';
    }

    // return buffer
    $output_string = ob_get_contents();;
    ob_end_clean();
    return $output_string;

}

// sets attributes to parameters or defaults if undeclared
function defaults($atts) {
    return shortcode_atts( array(
        'href' => '#',
        'number' => 5,
        'chars' => 250,
        'max_chars' => 500,
        'roll' => 'yes',
        'width' => 500,
    ), $atts );
}

// handles options of width input
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

// trim description text html
function content_trim($content) {
    return trim(strip_tags($content));
}

//sets length of preview
function preview_length_handler($atts, $content_len) {
    $prev_len = $atts['chars'];
    // if chars is set to full, make the preview the size of all of the content
    if ($prev_len == 'full') {
        $prev_len = $content_len;
    }
    return $prev_len;
}

// creates content string to proper size
function content_end_string_handler($content, $prev_len, $ext_len) {
    $content_end = substr($content, $prev_len, $ext_len);
    // if max_chars is full, then make the end the rest of the length
    if ($total_len == 'full') {
        $content_end = substr($content, $prev_len);
    }
    return $content_end;
}

function content_prev_string_handler($content, $prev_len) {
    return substr($content, 0, $prev_len);
}

// activates rolldown if true
function activate_roll ($prev_len, $content_len, $roll, $ext_len) {
    return $prev_len != "full" && $prev_len < $content_len && $roll == 'yes' && $ext_len > 0;
}

// adds the ellipses class if true
function extension_ellipses_handler ($total_len, $content_len) {
    if ($total_len < $content_len) {
        echo ', ellipses_ext';
    }
}

function title_trim ($title) {
    return trim(strip_tags($title));
}

?>