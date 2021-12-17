<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title>
        {{ $newsletter->getTitle() }}
    </title>
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <!--[if mso]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <!--[if lte mso 11]>
    <style type="text/css">
        .mj-outlook-group-fix { width:100% !important; }
    </style>
    <![endif]-->
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat" rel="stylesheet" type="text/css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css2?family=Montserrat);
    </style>
    <!--<![endif]-->
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
                max-width: 100%;
            }
        }
    </style>
    <style type="text/css">
    </style>
    <style type="text/css">
        a, .link { color: {{ $channel->getAccentColor() }} !important; }

        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #202a33;
            font-family: Montserrat, Verdana, sans-serif;
            margin-bottom: .5rem;
            margin-top: 0;
            line-height: 1.5;
            font-weight: 600;
        }

        .h1,
        h1 {
            font-size: 33px !important;
            line-height: 66px !important;
            padding-top: 33px !important;
        }

        .h2,
        h2 {
            font-size: 26px !important;
            line-height: 52px !important;
            padding-top: 26px !important;
        }

        .h3,
        h3 {
            font-size: 20px !important;
            line-height: 40px !important;
            padding-top: 20px !important;
        }

        .h4,
        h4 {
            font-size: 17px !important;
            font-weight: 600 !important;
        }

        .h5,
        h5 {
            font-size: 15px;
        }

        .h6,
        h6 {
            font-size: 14px;
        }

        p,
        ol,
        ul,
        li {
            font-family: Montserrat, Verdana, sans-serif;
            font-size: 18px !important;
            line-height: 1.52 !important;
            margin-bottom: 16px;
            margin-top: 0;
            color: #202a33
        }

        p:last-child {
            margin-bottom: 0
        }

        @media (min-width:768px) {
            p {
                font-size: 16px !important;
            }
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0
        }

        @media (min-width:768px) {

            .h1,
            h1 {
                font-size: 40px !important;
                line-height: 60px !important;
                padding-top: 40px !important;
            }
        }

        @media (min-width:768px) {

            .h2,
            h2 {
                font-size: 32px !important;
                line-height: 48px !important;
                padding-top: 32px !important;
            }
        }

        @media (min-width:768px) {

            .h3,
            h3 {
                font-size: 24px !important;
                line-height: 36px !important;
                padding-top: 24px !important;
            }
        }

        @media (min-width:768px) {

            .h4,
            h4 {
                font-size: 19px !important;
                line-height: 26px !important;
            }
        }

        @media (min-width:768px) {

            .h5,
            h5 {
                font-size: 17px !important;
                line-height: 25px !important;
            }
        }

        @media (min-width:768px) {
            p {
                font-size: 16px !important;
            }
        }

        @media (min-width:768px) {
            .text-size--regular {
                font-size: 16px !important;
            }
        }

        @media (min-width:768px) {
            .text-size--small {
                font-size: 17px !important;
            }
        }

        @media (min-width:768px) {

            p,
            ul,
            ol,
            li {
                font-size: 16px !important;
                font-weight: 500 !important;
            ;
            }
        }

        img {
            max-width: 100%;
        }

        p.h2 {
            padding-top: 0 !important;
        }
    </style>
</head>

<body style="word-spacing:normal;background-color:#ffffff;">
<div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    {{ $newsletter->getSubtitle() }}
</div>
<div style="background-color:#ffffff;">
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                    <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                    <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff;vertical-align:top;" width="100%">
                            <tbody>
                            <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:left;color:#202a33;">@isset($insertPixel) @if($insertPixel) @isset($pixel) @if (!empty($pixel)) <img src="{{ $pixel }}" alt=""> @endif @endisset @endif @endisset</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:14px;font-weight:500;line-height:1.5;text-align:right;color:#202a33;"><a href="*|ARCHIVE|*" target="_blank" style="box-sizing: border-box; float: right;">View online</a></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                    <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                    <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff;vertical-align:top;" width="100%">
                            <tbody>
                            <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:left;color:#202a33;">
                                        <!-- channel banner --> @if (!empty($banner)) <img src="{{ $banner }}" alt="Banner for letter {{ $newsletter->getId() }} in {{ $channel->getTitle() }}" style="max-width: 100%;"> @else <h1 class="h1">
                                            {{$channel->getTitle() }}
                                        </h1> @endif</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                    <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                    <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff;vertical-align:top;" width="100%">
                            <tbody>
                            <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:left;color:#202a33;">{!! $newsletter->getCopyRendered() !!}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                    <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                    <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff;vertical-align:top;" width="100%">
                            <tbody>
                            <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:left;color:#202a33;">Copyright © *|CURRENT_YEAR|* {{ $channel->getTitle() }}, All rights reserved.<br> *|IFNOT:ARCHIVE_PAGE|* *|LIST:DESCRIPTION|*</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:left;color:#202a33;"><b>Our mailing address:</b><br> *|HTML:LIST_ADDRESS_HTML|* *|END:IF|* <a class="link" href="*|UNSUB|*">unsubscribe from this list</a>
                                        <a class="link" href="*|UPDATE_PROFILE|*">update subscription preferences</a></div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Montserrat, Verdana, sans-serif;font-size:16px;font-weight:500;line-height:1.5;text-align:center;color:#202a33;"><br>
                                        <a class="link" href="https://link.whereby.us/nepno7s8ys"> Made with Letterhead <img alt="Letterhead logo" src="https://wherebyspace.nyc3.digitaloceanspaces.com/letterhead/letterhead_favicon1.png" style="width:20px;">
                                        </a></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--[if mso | IE]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
</div>
</body>

</html>
