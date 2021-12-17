<mjml lang="en">
    <mj-head>
        <mj-attributes>
            <mj-all background-color="#ffffff"
                    color="#202a33"
                    font-family="{{ $channel->getDefaultFont() }}, Verdana, sans-serif"
                    font-size="18px"
                    font-weight="500"
                    line-height="1.5" />
            <mj-class name="heading" />
        </mj-attributes>
        <mj-font name="{{ $channel->getDefaultFont() }}" href="https://fonts.googleapis.com/css2?family={{ $channel->getDefaultFont() }}:wght@400;500;600;700;900&display=swap" />
        <mj-font name="{{ $channel->getHeadingFont() }}" href="https://fonts.googleapis.com/css2?family={{ $channel->getHeadingFont() }}:wght@400;500;600;700;900&display=swap" />
        <mj-preview>{{ $newsletter->getSubtitle() }}</mj-preview>
        <mj-title>{{ $newsletter->getTitle() }}</mj-title>
        <mj-style>
        a, .link {
            color: {{ $channel->getAccentColor() }} !important;
        }
        </mj-style>
        <mj-style inline="inline">
        h1, h2, h3, .letter__subtitle {
            font-family: {{ $channel->getHeadingFont() }}, Verdana, sans-serif !important;
        }
        </mj-style> 
    </mj-head>
    <mj-body>
        <mj-section>
            <mj-column>
                <mj-image alt="" height="0px" src="{{ $newsletter->getTrackingPixel() }}" />

                <mj-text font-size="14px">
                    <a href="*|ARCHIVE|*" target="_blank">Read in your browser</a>
                </mj-text>
            </mj-column>
        </mj-section>
        <mj-section>
            <mj-column>
                @if (!empty($banner))
                    <mj-image alt="{{ $channel->getTitle() }}" src="{{ $banner }}" />
                @else
                    <mj-text css-class="heading">
                        <h1>{{ $channel->getTitle() }}</h1>
                    </mj-text>
                @endif
            </mj-column>
        </mj-section>

        {!! $newsletter->getMjmlTemplate() !!}

        <mj-section>
            <mj-column>
                <mj-text>
                    <p>Copyright © *|CURRENT_YEAR|* {{ $channel->getTitle() }}, All rights reserved.<br> *|IFNOT:ARCHIVE_PAGE|* *|LIST:DESCRIPTION|*</p>
                </mj-text>

                <mj-text>
                    <b>Our mailing address:</b><br> *|HTML:LIST_ADDRESS_HTML|* *|END:IF|* <a class="link" href="*|UNSUB|*">unsubscribe from this list</a>
                    <a class="link" href="*|UPDATE_PROFILE|*">update subscription preferences</a>
                </mj-text>

                <mj-text align="center">
                    <a  href="https://link.whereby.us/nepno7s8ys">Made with Letterhead <img alt="Letterhead logo" src="https://wherebyspace.nyc3.digitaloceanspaces.com/letterhead/letterhead_favicon1.png" style="width:20px;">
                    </a>
                </mj-text>
            </mj-column>
        </mj-section>
    </mj-body>
</mjml>
