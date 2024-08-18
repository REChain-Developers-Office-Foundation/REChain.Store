@php echo '
<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($languages as $language)
    <url>
        @if($settings['site_language'] == $lang_codes[$language->id] && $settings['root_language'] == '1')
          <loc>{{ asset($main_base_prefix) }}</loc>
       @else
        <loc>{{ asset("".$lang_codes[$language->id]) }}</loc>
       @endif
        <lastmod>{{ gmdate(DateTime::W3C, strtotime($lang_last_mod[$language->id])) }}</lastmod>
		<changefreq>{{ $settings['sitemap_home_changefreq'] }}</changefreq>
        <priority>{{ $settings['sitemap_home_priority'] }}</priority>
    </url>
    @endforeach
    @foreach ($addl_sitemaps as $row)
    <url>
        <loc>{{ $row->url }}</loc>
        <lastmod>{{ gmdate(DateTime::W3C, strtotime($row->last_update)) }}</lastmod>
        <changefreq>{{ $row->changefreq }}</changefreq>
        <priority>{{ $row->priority }}</priority>
    </url>
    @endforeach
</urlset>
