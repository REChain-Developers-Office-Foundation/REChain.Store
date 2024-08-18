{!! '<'.'?'.'xml version="1.0" encoding="UTF-8" ?>' !!}
 <rss version="2.0">
    <channel>
      <title>{{ $settings['site_title'] }}</title>
      <language>{{ $language_code }}</language>
      <link>{{ asset($language_prefix) }}</link>
      <description><![CDATA[{{ $settings['site_description'] }}]]></description>
      @foreach($news as $post)
      @if(empty($post->image))
      @php $post->image='no_image.png'; @endphp
      @endif
      <item>
        <title><![CDATA[{!! $post->title !!}]]></title>
        <pubDate>{{ $post->created_at->tz('UTC')->toRssString() }}</pubDate>
        <link>{{ asset($language_prefix.$settings['read_base'].'/'.$post->slug) }}</link>
        <guid isPermaLink="true">{{ asset($language_prefix.$settings['read_base'].'/'.$post->slug) }}</guid>
        <description><![CDATA[{!! $post->description !!}]]></description>
        <enclosure url="{{ s3_switch($post->image, 1) }}" type="image/png" />
      </item>
      @endforeach
    </channel>
  </rss>