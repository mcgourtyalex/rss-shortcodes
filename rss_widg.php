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
    ), $atts );

    $dom = new DOMDocument();
    $xml = $dom->load($atts['href']);

    if (!$xml) {
        echo "RSS feed unavailable!";
        return;
    }
    $items = $dom->getElementsByTagName('item');
    $count = 1;

    echo '<table style="border:none;">';
    foreach($items as $item) {
        echo '<tr><td style="padding-bottom: 0px; padding-top: 0px;">';
        $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
        $link = $item->getElementsByTagName('link')->item(0)->nodeValue;
        $content = $item->getElementsByTagName('description')->item(0)->nodeValue;
        $content_prev = substr($content,0,250).'...';
        $date = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;
        echo '<h5><a href="'.$link.'">'.$title.'</a></h5>';
        echo '</td></tr><tr><td style="background: #EFEFEF;">';
        echo $content;
        echo "<strong>"." ".$date."</strong><br />";
        echo '</td></tr><tr><td style="border:none;">';
        echo '</td></tr>';

        if ($count == $atts['number']) {
            break;
        }
        $count++;
    }
    echo '</table>';
}
?>