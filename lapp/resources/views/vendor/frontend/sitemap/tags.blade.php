@php $page_url = request()->getSchemeAndHttpHost();
echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($rows as $row)
        <url>
            <loc>{{$page_url}}/{{ $settings['tag_base'] }}/{{ $row->slug }}</loc>
            <lastmod>{{ gmdate(DateTime::W3C, strtotime($row->updated_at)) }}</lastmod>
            <changefreq>{{ $settings['sitemap_tag_changefreq'] }}</changefreq>
            <priority>{{ $settings['sitemap_tag_priority'] }}</priority>    
        </url>
    @endforeach
</urlset>