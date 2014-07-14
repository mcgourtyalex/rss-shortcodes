<style>
    .rss_content {
        padding: 10px;
        background: #EFEFEF;
    }
    
    .rss_content:after {
        content: " > more";
        font-weight: bold;
    }

    .rss_content:hover:after {
        content: "";
    }

    .rss_content_ext {
        display: none;
    }
    
    .rss_content:hover > span {
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
        $content_prev = substr($content,0,$atts['chars']);
        $content_end = substr($content,$atts['chars']);
        $date = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;
        echo '<h5><a href="'.$link.'">'.$title.'</a></h5>';
        echo '</td></tr><tr><td class="rss_td">';
        echo '<div class="rss_content">'.$content_prev;
        echo '<span class="rss_content_ext">'.$content_end.'</span>';
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