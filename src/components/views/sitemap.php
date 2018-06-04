<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 30.05.2016
 * Time: 23:44
 * @var array $urls
 * @var string $host
 */


echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
    if ($urls) foreach ($urls as $url) {
        echo "<url>\n";
        echo "<loc>{$host}{$url['url']}</loc>\n";
        echo "<priority>{$url['priority']}</priority>\n";
        if (isset($url['lastmod']))
            echo "<lastmod>{$url['lastmod']}</lastmod>\n";
        echo "</url>\n";
    }
    ?>
</urlset>