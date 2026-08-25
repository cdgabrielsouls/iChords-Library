<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $url)
    <url>
        <loc>{{ htmlspecialchars($url, ENT_XML1, 'UTF-8') }}</loc>
        <changefreq>monthly</changefreq>
    </url>
@endforeach
</urlset>