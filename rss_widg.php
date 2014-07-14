<style>
    .rss_content {
        padding: 10px;
        background: #EFEFEF;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .rss_content:hover {
        text-overflow: none;
        overflow: visible;
        -webkit-line-clamp: 10;
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
        echo '</td></tr><tr><td class="rss_td">';
        echo '<div class="rss_content">'.$content;
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
?>