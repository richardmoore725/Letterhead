<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style>
    body {
      font-family: Avenir, Helvetica, sans-serif;
      box-sizing: border-box;
      background-color: #202a33;
      color: #202a33;
      height: 100%;
      hyphens: auto;
      line-height: 1.4;
      margin: 0;
      -moz-hyphens: auto;
      -ms-word-break: break-all;
      width: 100% !important;
      -webkit-hyphens: auto;
      -webkit-text-size-adjust: none;
      word-break: break-word;
    }
  </style>
</head>
<body
      style="
      font-family: Avenir, Helvetica, sans-serif;
      box-sizing: border-box;
      background-color: #202a33;
      color: #202a33;
      height: 100%;
      hyphens: auto;
      line-height: 1.4;
      margin: 0;
      -moz-hyphens: auto;
      -ms-word-break: break-all;
      width: 100% !important;
      -webkit-hyphens: auto;
      -webkit-text-size-adjust: none;
      word-break: break-word;
    "
>
<style>
  @media only screen and (max-width: 600px) {
    .inner-body {
      width: 100% !important;
    }

    .footer {
      width: 100% !important;
    }
  }

  @media only screen and (max-width: 500px) {
    .button {
      width: 100% !important;
    }
  }
</style>
<table
      class="wrapper"
      width="100%"
      cellpadding="0"
      cellspacing="0"
      style="
        font-family: Avenir, Helvetica, sans-serif;
        box-sizing: border-box;
        background-color: #202a33;
        margin: 0;
        padding: 0;
        width: 100%;
        -premailer-cellpadding: 0;
        -premailer-cellspacing: 0;
        -premailer-width: 100%;
      "
>
  <tr>
    <td
        align="center"
        style="
          font-family: Avenir, Helvetica, sans-serif;
          box-sizing: border-box;
        "
    >
      <table
        class="content"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        style="
          font-family: Avenir, Helvetica, sans-serif;
          box-sizing: border-box;
          margin: 0;
          padding: 0;
          width: 100%;
          -premailer-cellpadding: 0;
          -premailer-cellspacing: 0;
          -premailer-width: 100%;
        "
      >
        <tr>
          <td
            class="header"
            style="
              font-family: Avenir, Helvetica, sans-serif;
              box-sizing: border-box;
              padding: 25px 0;
              text-align: center;
            "
          >
            <a
              href="https://tryletterhead.com"
              style="
                font-family: Avenir, Helvetica, sans-serif;
                box-sizing: border-box;
                color: #ffffff;
                font-size: 19px;
                font-weight: bold;
                text-decoration: none;
              "
            >
              <img src="https://nyc3.digitaloceanspaces.com/wherebyspace/services/mailservice/MasterLogo-Alternate.png"
                   style="width: 250px"
                   alt="Letterhead">
            </a>
          </td>
        </tr>
        <!-- Email Body -->
        <tr>
          <td
                class="body"
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                  font-family: Avenir, Helvetica, sans-serif;
                  box-sizing: border-box;
                  background-color: #202a33;
                  border-bottom: 1px solid #202a33;
                  border-top: 1px solid #202a33;
                  margin: 0;
                  padding: 0;
                  width: 100%;
                  -premailer-cellpadding: 0;
                  -premailer-cellspacing: 0;
                  -premailer-width: 100%;
                "
          >
            <table
                  class="inner-body"
                  align="center"
                  width="570"
                  cellpadding="0"
                  cellspacing="0"
                  style="
                    font-family: Avenir, Helvetica, sans-serif;
                    box-sizing: border-box;
                    background-color: #f5fcff;
                    margin: 0 auto;
                    padding: 0;
                    width: 570px;
                    -premailer-cellpadding: 0;
                    -premailer-cellspacing: 0;
                    -premailer-width: 570px;
                  "
            >
              <!-- Body content -->
              <tr>
                <td
                  class="content-cell"
                  style="
                  font-family: Avenir, Helvetica, sans-serif;
                  box-sizing: border-box;
                  padding: 35px;
                "
                >
                  <h1
                    style="
                    font-family: Avenir, Helvetica, sans-serif;
                    box-sizing: border-box;
                    color: #202a33;
                    font-size: 19px;
                    font-weight: bold;
                    margin-top: 0;
                    text-align: left;
                  "
                  >
                    {{ $heading }}
                  </h1>
                  <div
                    style="
                    font-family: Avenir, Helvetica, sans-serif;
                    box-sizing: border-box;
                    color: #202a33;
                    font-size: 16px;
                    line-height: 1.5em;
                    margin-top: 0;
                    text-align: left;
                        "
                  >
                    {!! $copy !!}
                  </div>
                  <p>
                    <a
                      href="{{ $callToActionUrl }}"
                      class="button button-green"
                      target="_blank"
                      style="
                      font-family: Avenir, Helvetica,
                      sans-serif;
                      box-sizing: border-box;
                      border-radius: 3px;
                      box-shadow: 0 2px 3px
                      rgba(0, 0, 0, 0.16);
                      color: #ffffff;
                      display: inline-block;
                      text-decoration: none;
                      -webkit-text-size-adjust: none;
                      background-color: #066893;
                      border-top: 10px solid #066893;
                      border-right: 18px solid #066893;
                      border-bottom: 10px solid #066893;
                      border-left: 18px solid #066893;
                    ">
                    {{ $callToAction }}
                    </a>
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td
            style="
              font-family: Avenir, Helvetica, sans-serif;
              box-sizing: border-box;
            "
          >
            <table
              class="footer"
              align="center"
              width="570"
              cellpadding="0"
              cellspacing="0"
              style="
              font-family: Avenir, Helvetica, sans-serif;
              box-sizing: border-box;
              margin: 0 auto;
              padding: 0;
              text-align: center;
              width: 570px;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              -premailer-width: 570px;
            "
            >
              <tr>
                <td
                  class="content-cell"
                  align="center"
                  style="
                  font-family: Avenir, Helvetica, sans-serif;
                  box-sizing: border-box;
                  padding: 35px;
                "
                >
                  <p
                    style="
                    font-family: Avenir, Helvetica, sans-serif;
                    box-sizing: border-box;
                    line-height: 1.5em;
                    margin-top: 0;
                    color: #ffffff;
                    font-size: 12px;
                    text-align: center;
                  "
                  >
                    © <?php echo date('Y'); ?> Letterhead. All rights reserved.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
