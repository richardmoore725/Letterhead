<?php

namespace App\DTOs;

/**
 * @package App\DTOs
 * @see https://airtable.com/tbltoyzycjaOSbWoK/viwkJrDvYNIJHsZ9O/recj192HXcSiHad4j?blocks=hide
 */
class ChannelDto
{
    public $accentColor;
    public $brandId;
    public $channelConfigurations;
    public $channelHorizontalLogo;
    public $channelSlug;
    public $channelDescription;
    public $channelImage;
    public $channelSquareLogo;
    public $defaultEmailFromName;
    public $defaultEsp;
    public $defaultFromEmailAddress;
    public $defaultFont;
    public $deletedAt;
    public $enableChannelAuthoring;
    public $hasValidMailChimpKey;
    public $headingFont;
    public $loadPromosBeforeHeadings;
    public $title;
    public $timeSinceMailChimpStatusPinged;
    public $ccAccessToken;
    public $ccRefreshToken;
    public $ccAccessTokenLastUsed;
    public $createdAt;
    public $id;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->accentColor = isset($object->accentColor) ? $object->accentColor : '';
        $this->brandId = isset($object->brandId) ? $object->brandId : null;
        $this->channelConfigurations = isset($object->channelConfigurations) ? $object->channelConfigurations : null;
        $this->channelHorizontalLogo = isset($object->channelHorizontalLogo) ? $object->channelHorizontalLogo : '';
        $this->channelSlug = isset($object->channelSlug) ? $object->channelSlug : '';
        $this->channelDescription = isset($object->channelDescription) ? $object->channelDescription : '';
        $this->channelImage = isset($object->channelImage) ? $object->channelImage : '';
        $this->channelSquareLogo = isset($object->channelSquareLogo) ? $object->channelSquareLogo : '';
        $this->defaultEmailFromName = isset($object->defaultEmailFromName) ? $object->defaultEmailFromName : '';
        $this->defaultEsp = isset($object->defaultEsp) ? $object->defaultEsp : 0;
        $this->defaultFromEmailAddress = isset($object->defaultFromEmailAddress) ? $object->defaultFromEmailAddress : '';
        $this->defaultFont = isset($object->defaultFont) ? $object->defaultFont : '';
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : null;
        $this->enableChannelAuthoring = isset($object->enableChannelAuthoring) ? $object->enableChannelAuthoring : false;
        $this->hasValidMailChimpKey = isset($object->hasValidMailChimpKey) ? $object->hasValidMailChimpKey : false;
        $this->headingFont = isset($object->headingFont) ? $object->headingFont : '';
        $this->loadPromosBeforeHeadings = isset($object->loadPromosBeforeHeadings) ? $object->loadPromosBeforeHeadings : false;
        $this->title = isset($object->title) ? $object->title : '';
        $this->timeSinceMailChimpStatusPinged = isset($object->timeSinceMailChimpStatusPinged) ? $object->timeSinceMailChimpStatusPinged : '';
        $this->ccAccessToken = isset($object->ccAccessToken) ? $object->ccAccessToken : '';
        $this->ccRefreshToken = isset($object->ccRefreshToken) ? $object->ccRefreshToken : '';
        $this->ccAccessTokenLastUsed = isset($object->ccAccessTokenLastUsed) ? $object->ccAccessTokenLastUsed : null;
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    public function mapChannelDtoToDatabaseColumns(): array
    {
        return [
            'accentColor' => $this->accentColor,
            'brandId' => $this->brandId,
            'created_at' => $this->createdAt,
            'channelHorizontalLogo' => $this->channelHorizontalLogo,
            'channelSlug' => $this->channelSlug,
            'channelSquareLogo' => $this->channelSquareLogo,
            'channelDescription' => $this->channelDescription,
            'channelImage' => $this->channelImage,
            'defaultEmailFromName' => $this->defaultEmailFromName,
            'defaultFromEmailAddress' => $this->defaultFromEmailAddress,
            'ccAccessToken' => $this->ccAccessToken,
            'ccRefreshToken' => $this->ccRefreshToken,
            'ccAccessTokenLastUsed' => $this->ccAccessTokenLastUsed,
            'defaultFont' => $this->defaultFont,
            'deleted_at' => $this->deletedAt,
            'enableChannelAuthoring' => $this->enableChannelAuthoring,
            'hasValidMailChimpKey' => $this->hasValidMailChimpKey,
            'headingFont' => $this->headingFont,
            'id' => $this->id,
            'loadPromosBeforeHeadings' => $this->loadPromosBeforeHeadings,
            'title' => $this->title,
            'updated_at' => $this->updatedAt,
            'timeSinceMailChimpStatusPinged' => $this->timeSinceMailChimpStatusPinged,
        ];
    }
}
