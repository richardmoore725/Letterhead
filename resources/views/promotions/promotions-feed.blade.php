<?=
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom">

    <channel>
        <atom:link href="https://google.com" rel="self" type="application/rss+xml"></atom:link>
        <description>A private feed of content promoted in {{ $channel->getTitle() }}</description>
        <docs></docs>
        <language>en-US</language>
        <link>https://tryletterhead.com</link>
        <generator>Letterhead</generator>
        <lastBuildDate></lastBuildDate>
        <managingEditor></managingEditor>
        <image>
            <url>{{ $channel->getChannelImage() }}</url>
            <title>A private feed of content promoted in {{ $channel->getTitle() }}</title>
            <link>https://tryletterhead.com</link>
        </image>
        <pubDate></pubDate>
        <title>{{ $channel->getTitle() }}</title>

        @foreach($promotions as $promotion)
            <item>
                <category>Promotion</category>
                <comments></comments>
                <content:encoded>{{ $promotion->getMarkup() }}</content:encoded>
                <dc:creator>{{ $promotion->getPromoterDisplayName() }}</dc:creator>
                <description>
                    {{ $promotion->getBlurb() }}
                    {{ $promotion->getContent() }}
                </description>
                <link>{{ $promotion->getResolvedCallToActionUrl() }}</link>

                @if (!empty($promotion->getPromoterImage()))
                    <media:content
                            url="{{ $promotion->getPromoterImage() }}"
                            medium="image"
                            type="image/jpg"
                            isDefault="true"
                            lang="en"></media:content>
                @endif

                <pubDate>{{ $promotion->getDateStart() }}</pubDate>
                <title>{{ $promotion->getHeading() }}</title>
            </item>
        @endforeach
    </channel>
</rss>
