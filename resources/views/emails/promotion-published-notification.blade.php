<!--
 # $PromotionPublishedNotification template
 This is the core content for a Promotion Published Notification we send to users.
 -->
<p>
    Success! The following promotion was published in {{ $channelName }}. We'll send you a summary
    of how it's performing later today
</p>

@include('promotions.promotion-table', $arrayOfPromotionPropertiesToEmail)
