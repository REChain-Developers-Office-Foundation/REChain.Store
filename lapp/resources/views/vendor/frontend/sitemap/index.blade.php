@php echo '
<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <sitemap>
            <loc>{{ asset('addl-sitemap.xml') }}</loc>
        </sitemap>
    @if ($total_apps > 0)
    @php $numberofpages = ceil($total_apps / $settings['sitemap_records_per_page']); @endphp
    @for ($i = 0; $i < $numberofpages; $i++) <sitemap>
        <loc>{{ asset('/app-sitemap.xml') }}?page={{$i+1}}</loc>
        </sitemap>
        @endfor
        @endif
        @if ($total_categories > 0)
        <sitemap>
            @php $numberofpages = ceil($total_categories / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('category-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
        @if ($total_platforms > 0)
        <sitemap>
            @php $numberofpages = ceil($total_platforms / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('/platform-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
        @if ($total_news > 0)
        <sitemap>
            @php $numberofpages = ceil($total_news / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('/news-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
        @if ($total_topics > 0)
        <sitemap>
            @php $numberofpages = ceil($total_topics / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('/topic-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
        @if ($total_pages > 0)
        <sitemap>
            @php $numberofpages = ceil($total_pages / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('/page-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
        @if ($total_tags > 0)
        <sitemap>
            @php $numberofpages = ceil($total_tags / $settings['sitemap_records_per_page']); @endphp
            @for ($i = 0; $i < $numberofpages; $i++) <loc>{{ asset('/tag-sitemap.xml') }}?page={{$i+1}}</loc>
                @endfor
        </sitemap>
        @endif
</sitemapindex>