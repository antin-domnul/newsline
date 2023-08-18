<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Современные новости</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .news-info {
            flex: 1;
            padding-left: 20px;
        }
        .news-item h2 {
            margin: 0;
            font-size: 18px;
        }
        .news-item p {
            color: #555;
            margin: 5px 0;
        }
        .news-item a {
            color: #007bff;
            text-decoration: none;
        }
        .news-item a:hover {
            text-decoration: underline;
        }
        .news-item .source {
            font-size: 14px;
            color: #888;
        }
        .news-item .pubDate {
            font-size: 14px;
            color: #888;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Современные новости</h1>
    </header>
    <div class="container">
    <?php
    // Массив с URL-ами RSS лент
    $rss_feed_urls = array(
        "https://www.rbc.ua/static/rss/all.rus.rss.xml",
        "https://www.pravda.com.ua/rss/",
        "https://www.radiosvoboda.org/api/zrqiteuuir",
        "https://www.liga.net/news/all/rss.xml",
        "https://fakty.ua/rss_feed/all"
    );

    // Массив для хранения уникальных новостей
    $unique_news = array();

    // Функция для вычисления хэша на основе заголовка и описания
    function computeHash($title, $description) {
        return md5($title . $description);
    }

    // Получаем новости из каждой ленты
    foreach ($rss_feed_urls as $url) {
        $rss_feed = simplexml_load_file($url);
        foreach ($rss_feed->channel->item as $item) {
            $title = (string)$item->title;
            $description = (string)$item->description;
            $hash = computeHash($title, $description);

            $isDuplicate = false;
            foreach ($unique_news as $unique_item) {
                $unique_hash = $unique_item["hash"];
                // Проверяем хэш текущей новости с хэшами уникальных новостей
                // Если хэши близки (например, похожи на строковом уровне),
                // то считаем новость дубликатом
                if (similar_text($hash, $unique_hash) > 15) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $unique_news[] = array(
                    "hash" => $hash,
                    "title" => $title,
                    "link" => (string)$item->link,
                    "description" => $description,
                    "pubDate" => strtotime($item->pubDate), // Преобразуем дату в timestamp
                    "source" => $url
                );
            }
        }
    }

    // Сортируем уникальные новости по времени публикации в убывающем порядке
    usort($unique_news, function ($a, $b) {
        return $b["pubDate"] - $a["pubDate"];
    });

    // Выводим отсортированные уникальные новости
    //foreach ($unique_news as $news_item) {
    //    $title = $news_item["title"];
    //    $link = $news_item["link"];
     //   $description = $news_item["description"];
    //    $pubDate = date("H:i", $news_item["pubDate"]);
    //    $source = $news_item["source"];

    //    echo "<h2><a href='$link'>$title</a></h2>";
    //    echo "<p><strong>Источник:</strong> $source</p>";
    //    echo "<p><strong>$pubDate</strong></p>";
    //    echo "<p>$description</p>";
    //}
    ?>
        <?php foreach ($unique_news as $news_item): ?>
            <div class="news-item">
                <div class="pubDate"><?= date('H:i', $news_item['pubDate']) ?></div>
                <div class="news-info">
                    <h2><a href="<?= $news_item['link'] ?>"><?= $news_item['title'] ?></a></h2>
                    <p><?= $news_item['description'] ?></p>
                    <p class="source"><?= parse_url($news_item['source'], PHP_URL_HOST) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>