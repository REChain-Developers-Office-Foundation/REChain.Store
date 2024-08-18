@php echo '
<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @foreach ($rows as $row)
    <url>
        @if(isset($row->lang_id, $lang_codes[$row->lang_id]))
        <loc>{{ asset("".$lang_codes[$row->lang_id]."/".$slug[$row->lang_id]['topic_base']."/$row->slug") }}</loc>
        @else
        <loc>{{ asset("".$main_base_prefix."/".$slug[$main_site_id]['topic_base']."/$row->slug") }}</loc>
        @endif
        <lastmod>{{ gmdate(DateTime::W3C, strtotime($row->updated_at)) }}</lastmod>
        <changefreq>{{ $settings['sitemap_topic_changefreq'] }}</changefreq>
        <priority>{{ $settings['sitemap_topic_priority'] }}</priority>
    </url>
    @endforeach
</urlset>
