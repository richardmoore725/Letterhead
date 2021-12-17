<!--
 # $PromotionPublishedNotification template
 This is the core content for a Promotion Published Notification we send to users.
 -->
<p>
    Hey there. Here is a day-one summary of your metrics for your promotion
    in {{ $channelName }}. We update metrics every few hours, so feel free
    to check-in on them at any time.
</p>

@include('promotions.promotion-metrics-table', $arrayOfPromotionMetricsPropertiesToEmail)

<h3>Your promotion</h3>

@include('promotions.promotion-table', $arrayOfPromotionPropertiesToEmail)
