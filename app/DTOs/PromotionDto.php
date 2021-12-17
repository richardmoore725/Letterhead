<?php

namespace App\DTOs;

use App\Models\Promotion;

class PromotionDto
{
    public $adTypeId;
    public $advertiserLogo;
    public $blurb;
    public $brandId;
    public $callToAction;
    public $callToActionUrl;
    public $channelId;
    public $content;
    public $dateStart;
    public $emoji;
    public $heading;
    public $id;
    public $markup;
    public $metrics;
    public $mjml;
    public $pixel;
    public $positioning;
    public $promoterDisplayName;
    public $promoterImage;
    public $promoterImageAlternativeText;
    public $promotionArray;
    public $promotionObject;
    public $resolvedCallToActionUrl;
    public $uniqueId;
    public $scheduledDate;
    public $status;

    public function __construct(array $promotionArray = null, \stdClass $promotionObject = null)
    {
        $this->promotionArray = $promotionArray;
        $this->promotionObject = $promotionObject;

        if (!empty($promotionArray)) {
            $this->adTypeId = $this->getKey('adTypeId', 0);
            $this->advertiserLogo = $this->getKey('advertiserLogo', '');
            $this->blurb = $this->getKey('blurb', '');
            $this->brandId = $this->getKey('brandId', 0);
            $this->callToAction = $this->getKey('callToAction', '');
            $this->callToActionUrl = $this->getKey('callToActionUrl', '');
            $this->channelId = $this->getKey('channelId', 0);
            $this->content = $this->getKey('content', '');
            $this->dateStart = $this->getKey('dateStart', '');
            $this->emoji = $this->getKey('emoji', '');
            $this->heading = $this->getKey('heading', '');
            $this->id = $this->getKey('id', 0);
            $this->markup = $this->getKey('markup', '');
            $this->metrics = $this->getKey('metrics', null);
            $this->mjml = $this->getKey('mjml', '');
            $this->pixel = $this->getKey('pixel', '');
            $this->positioning = $this->getKey('positioning', 0);
            $this->promoterDisplayName = $this->getKey('promoterDisplayName', '');
            $this->promoterImage = $this->getKey('promoterImage', '');
            $this->promoterImageAlternativeText = $this->getKey('promoterImageAlternativeText', '');
            $this->promotionArray = $this->getKey('promotionArray', '');
            $this->resolvedCallToActionUrl = $this->getKey('resolvedCallToActionUrl', '');
            $this->uniqueId = $this->getKey('uniqueId', '');
            $this->scheduledDate = $this->getKey('scheduledDate', '');
            $this->status = $this->getKey('status', Promotion::STATUS_NEWLY_CREATED);
            return;
        }

        if (!empty($promotionObject)) {
            $this->adTypeId = $this->getProperty('adTypeId', 0);
            $this->advertiserLogo = $this->getProperty('advertiserLogo', '');
            $this->blurb = $this->getProperty('blurb', '');
            $this->brandId = $this->getProperty('brandId', 0);
            $this->callToAction = $this->getProperty('callToAction', '');
            $this->callToActionUrl = $this->getProperty('callToActionUrl', '');
            $this->channelId = $this->getProperty('channelId', 0);
            $this->content = $this->getProperty('content', '');
            $this->dateStart = $this->getProperty('dateStart', '');
            $this->emoji = $this->getProperty('emoji', '');
            $this->heading = $this->getProperty('heading', '');
            $this->id = $this->getProperty('id', 0);
            $this->markup = $this->getProperty('markup', '');
            $this->metrics = $this->getProperty('metrics', null);
            $this->mjml = $this->getProperty('mjml', '');
            $this->pixel = $this->getProperty('pixel', '');
            $this->positioning = $this->getProperty('positioning', 0);
            $this->promoterDisplayName = $this->getProperty('promoterDisplayName', '');
            $this->promoterImage = $this->getProperty('promoterImage', '');
            $this->promoterImageAlternativeText = $this->getProperty('promoterImageAlternativeText', '');
            $this->promotionArray = $this->getProperty('promotionArray', '');
            $this->resolvedCallToActionUrl = $this->getProperty('resolvedCallToActionUrl', '');
            $this->uniqueId = $this->getProperty('uniqueId', '');
            $this->scheduledDate = $this->getProperty('scheduledDate', '');
            $this->status = $this->getProperty('status', Promotion::STATUS_NEWLY_CREATED);
            return;
        }
    }

    public function convertToModel(): Promotion
    {
        return new Promotion($this);
    }

    /**
     * So as not to have to rewrite an isset over and over. With this method we can
     * set the Dto with fewer lines of code.
     *
     * @param string $propertyName
     * @param $defaultValue
     * @return mixed
     */
    private function getKey(string $propertyName, $defaultValue)
    {
        return isset($this->promotionArray[$propertyName]) ? $this->promotionArray[$propertyName] : $defaultValue;
    }

    private function getProperty(string $propertyName, $defaultValue)
    {
        return isset($this->promotionObject->$propertyName) ? $this->promotionObject->$propertyName : $defaultValue;
    }
}
