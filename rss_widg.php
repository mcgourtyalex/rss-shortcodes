<?php
/*
Plugin Name: RSS Widget
Description: Embeds RSS in pages and posts with shortcodes
*/

define('RSS_WIDG_PATH', plugin_dir_path( __FILE__ ) );

//require_once( RSS_WIDG_PATH . '/shortcode_content.php' );

add_shortcode( 'rss_embed', 'rss_embed_content' );

function rss_embed_content($atts) {

    $atts = shortcode_atts( array(
        'href' => '#',
        'number' => 5,
    ), $atts );

    $dom = new DOMDocument();
    $xml = $dom->load($atts['href']);

    if (!$xml) {
        echo "RSS feed unavailable!";
        return;
    }
    $items = $dom->getElementsByTagName('item');
    $count = 1;

    foreach($items as $item) {
        $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
        $link = $item->getElementsByTagName('link')->item(0)->nodeValue;
        $content = $item->getElementsByTagName('description')->item(0)->nodeValue;
        $date = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;
        echo '<h4><a href="'.$link.'">'.$title.'</a></h4>';
        echo $content."<br />";
        echo "<strong>".$date."</strong><br />";

        if ($count == $atts['number']) {
            break;
        }
        $count++;
    }

}

?>