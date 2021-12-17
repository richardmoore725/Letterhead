<!--
 # $Promotion Table (for Email)
 Our transactional emails often include a user's Promotion, which is like an advertisement.
 This blade template is an abstraction that will allow us to include it in multiple emails
 in a mostly DRY way. Note the inline styles. We do this to ensure the best cross-email client
 compatibility.
-->
<div class='table' style='font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;'>
    <table style='font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 30px auto; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;'>
        <tbody style='font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;'>

        @foreach ($arrayOfPromotionPropertiesToEmail as $promotionPropertyName => $promotionPropertyValue)
            @if (!empty($promotionPropertyValue))
                <tr>
                    <td  style=' font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #202a33; font-size: 15px; font-weight: bold; line-height: 18px; padding: 10px 0;'>
                        {{ $promotionPropertyName }}
                    </td>
                    <td style='font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #202a33;font-size: 15px;line-height: 18px;padding: 10px 0;'>
                        {{ $promotionPropertyValue }}
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
