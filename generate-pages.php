<?php
// LitGamez SEO Game Pages Generator

$apiUrl = 'https://litgamez.com/api/getAllGames?getAll=true&popular=true&v=1';
$outputDir = __DIR__ . '/play';
$template = file_get_contents(__DIR__ . '/template.html');

// Ensure output directory exists
if (!is_dir($outputDir)) mkdir($outputDir, 0755, true);

// Fetch game data from API
$data = json_decode(file_get_contents($apiUrl), true);
$games = $data['data'] ?? [];

// Generate game detail pages
foreach ($games as $game) {
    $slug = $game['slug'];
    $title = htmlspecialchars($game['name']);
    $desc = $game['descr'];
    // $short_desc = htmlspecialchars(strip_tags($game['descr']));
    $short_desc_1 = substr(htmlspecialchars(strip_tags($desc)), 0, 160);

    $cleaned = strip_tags($short_desc_1);
    $cleaned = preg_replace("/\s+/", " ", $cleaned);
    $short_desc = trim($cleaned);

    $link = "https://litgamez.com/en/g/{$slug}";

    $page = str_replace(
        ['{{TITLE}}', '{{DESCRIPTION}}', '{{SHORT_DESCRIPTION}}', '{{SLUG}}', '{{CANONICAL}}'],
        [$title, nl2br($desc), $short_desc, $slug, $link],
        $template
    );

    file_put_contents("{$outputDir}/{$slug}.html", $page);
}
echo "✅ Generated " . count($games) . " game pages.\n";

// Generate index.html
$indexContent = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Popular Games FREE Online | LitGamez</title>
  <meta name="description" content="Browse 10000+ free online games only at LitGamez.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet" />
    <link rel="apple-touch-icon" href="https://litgamez.com/img/logo/favicon-hd.png" type="application/octet-stream" sizes="180x180" />
    <link rel="apple-touch-icon" href="https://litgamez.com/img/logo/favicon-hd.png" type="application/octet-stream" sizes="167x167" />
    <link rel="apple-touch-icon" href="https://litgamez.com/img/logo/favicon-hd.png" type="application/octet-stream" sizes="152x152" />
    <link rel="apple-touch-icon" href="https://litgamez.com/img/logo/favicon-hd.png" type="application/octet-stream" sizes="120x120" />
    <link rel="shortcut icon" href="https://litgamez.com/favicon.ico?v=1">
    <link rel="icon" href="https://litgamez.com/favicon.ico?v=1" type="image/x-icon" size="16x16" />
    <link rel="icon" type="image/png" href="https://litgamez.com/favicon.ico?v=1" />

  <link rel="stylesheet" href="/style.css" />
</head>
<body>
  <h1 class="text-center text-white">Online Free Games - Play 24x7</h1>
  <p class="text-center text-white">Enjoy free online games anytime, anywhere—no downloads needed! Explore action, puzzle, and multiplayer games with smooth gameplay, great graphics, and endless fun for solo players or friends.</p>
  <ul class="grid-view">
HTML;

foreach ($games as $game) {
    $slug = htmlspecialchars($game['slug']);
    $name = htmlspecialchars($game['name']);
    $logo = htmlspecialchars($game['logo']);
    $indexContent .= "    <li><a href=\"/play/{$slug}.html\"><img src='https://litgamez.com/uploads/logos/{$logo}' alt='{$name}' /><h3>{$name}</h3></a></li>\n";
}

$indexContent .= <<<HTML
  </ul>
  <footer>
    <p>Visit the full game site at <a href="https://litgamez.com" target="_blank" rel="nofollow">LitGamez.com</a></p>
  </footer>
</body>
</html>
HTML;

file_put_contents(__DIR__ . '/index.html', $indexContent);
echo "✅ Generated index.html\n";

// Generate sitemap.xml
$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$sitemap .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($games as $game) {
    $slug = $game['slug'];
    $sitemap .= "  <url>\n";
    $sitemap .= "    <loc>https://litgamezpro.github.io/play/{$slug}.html</loc>\n";
    $sitemap .= "  </url>\n";
}
$sitemap .= "</urlset>";
file_put_contents(__DIR__ . '/sitemap.xml', $sitemap);
echo "✅ Generated sitemap.xml\n";
