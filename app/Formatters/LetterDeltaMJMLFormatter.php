<?php

namespace App\Formatters;

use App\Collections\UserCollection;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\Promotion;
use nadar\quill\Lexer;
use nadar\quill\listener\Image;
use nadar\quill\listener\Text;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Symfony\Component\DomCrawler\Crawler;

class LetterDeltaMJMLFormatter implements LetterDeltaMJMLFormatterInterface
{
    private function generateLetterBylineFromArrayOfAuthors(array $arrayOfAuthors): string
    {
        if (empty($arrayOfAuthors)) {
            return 'Unknown author';
        }

        if (sizeof($arrayOfAuthors) === 1) {
            return implode('|', $arrayOfAuthors);
        }

        if (sizeof($arrayOfAuthors) === 2) {
            return implode(' and ', $arrayOfAuthors);
        }

        $arrayOfNamesJoinWithComas = array_slice($arrayOfAuthors, 0, sizeof($arrayOfAuthors) - 1);
        $stringOfNamesJoinWithComas = implode(', ', $arrayOfNamesJoinWithComas);

        return $stringOfNamesJoinWithComas . ' and ' . array_pop($arrayOfAuthors);
    }

    private function generateMjmlFromDeltaAndPromotions(
        string $byline,
        string $delta,
        array $promotions,
        bool $loadPromosBeforeHeadings,
        string $title,
        string $subtitle
    ): Response {
        $transformation = $this->transformDeltaToHtml($byline, $delta, $title, $subtitle);

        if ($transformation->isError()) {
            return $transformation;
        }

        $markupBeforeInsertPromos = $transformation->getData();
        $insertPromotionsResponse = $this->insertPromotionMjmlInLetterMjml($markupBeforeInsertPromos, $promotions, $loadPromosBeforeHeadings);

        if ($insertPromotionsResponse->isError()) {
            return $insertPromotionsResponse;
        }

        $markupWithPromotions = $insertPromotionsResponse->getData();

        $cleanedUpMarkup = str_replace(
            [
              '<p></p>',
              '<mj-text></mj-text>',
              '</mj-text><mj-text>',
              '<p><mj-image',
              '</p><mj-image',
              '></mj-image></p><p>',
              '</mj-text><mj-section',
              '</mj-section><mj-text',
              '</mj-text><mj-wrapper',
              '</mj-wrapper><mj-text',
              '</h2><mj-image',
              '</h3><mj-image',
              '></mj-image></p></mj-text>',
              '</mj-text><mj-raw',
              '</mj-raw><mj-text',
              '<mj-section><mj-column><mj-section',
              '/><mj-raw',
            ],
            [
               '',
               '',
               '',
               '<mj-image',
                '</p></mj-text><mj-image',
               '/><mj-text><p>',
                '</mj-text></mj-column></mj-section><mj-section',
                '</mj-section><mj-section><mj-column><mj-text',
                '</mj-text></mj-column></mj-section><mj-wrapper',
                '</mj-wrapper><mj-section><mj-column><mj-text',
                '</h2></mj-text><mj-image',
                '</h3></mj-text><mj-image',
                '/>',
                '</mj-text></mj-column></mj-section><mj-raw',
                '</mj-raw><mj-section><mj-column><mj-text',
                '<mj-section',
                '/></mj-column></mj-section><mj-raw',
            ],
            $markupWithPromotions['markup']
        );

        $wrappedMarkup = str_starts_with($cleanedUpMarkup, '<mj-wrapper')
            ? "{$cleanedUpMarkup}</mj-column></mj-section>"
            : "<mj-section><mj-column>{$cleanedUpMarkup}</mj-column></mj-section>";

        return new Response('', 200, $wrappedMarkup);
    }

    /**
     * Given a string of valid (?) `mjml`, and an array of Promotion objects, we will try to
     * to insert the promotions into the string.
     *
     * @param string $mjml
     * @param array $promotions
     * @see https://www.php.net/manual/en/function.libxml-use-internal-errors.php
     * @return Response
     */
    private function insertPromotionMjmlInLetterMjml(string $mjml, array $promotions, bool $insertPromotionMjmlInLetterMjml): Response
    {
        /**
         * Let's turn on Libxm errors so that we can catch them. This will catch validation errors
         * and other problems with our MJML template.
         */
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $mjml);
        $crawler = new Crawler($doc);

        $nodes = $insertPromotionMjmlInLetterMjml
            ? $crawler->filter('mj-text[css-class*="heading--h1"], mj-text[css-class*="heading--h2"]')
            : $crawler->filter('mj-text');

        $errors = [];

        /**
         * @var Promotion
         */
        foreach ($promotions as $promotion) {
            $mjml = $promotion->getMjml();

            if (empty($mjml)) {
                $errors[] = "Promotion {$promotion->getId()} doesn't have an MJML template'";
                continue;
            }

            $positioningAsPercentage = $promotion->getPositioning() / 100;

            /**
             * Lets find the index in the array of heading nodes that is nearest to the
             * position of the promotion.
             */
            $indexOfNodeNearetToPromotionPosition = $positioningAsPercentage === 1
                ? (int) floor($nodes->count() * $positioningAsPercentage - 1)
                : (int) floor($nodes->count() * $positioningAsPercentage);

            /**
             * We'll get the node at the place of this index.
             */
            $nodeToInsertPromo = $nodes->getNode($indexOfNodeNearetToPromotionPosition);

            if (!empty($nodeToInsertPromo)) {
                /**
                 * We need to create a DOM Document fragment that we then append the promotion's
                 * MJML to. This fragment will then be able to be inserted into the larger part
                 * of the DOM.
                 */
                $fragment = $doc->createDocumentFragment();

                $test = str_replace(
                    [
                        "'0'",
                        "'0px'",
                        "=''",
                        "='",
                        "png'",
                        "jpg'",
                        "gif'",
                        "jpeg'",
                        "' />",
                        "'>",
                        '&',
                        "center'",
                        "px'",
                        "' title=",

                    ],
                    [
                        '"0"',
                        '"0px"',
                        '=""',
                        '="',
                        'png"',
                        'jpg"',
                        'gif"',
                        'jpeg"',
                        '" />',
                        '">',
                        '&amp;',
                        'center"',
                        'px"',
                        '" title=',
                    ],
                    $mjml
                );

                $fragment->appendXML($test);

                foreach (libxml_get_errors() as $error) {
                    // Ignore unknown tag errors
                    if ($error->code === 801) {
                        continue;
                    }

                    $errors[] = "line {$error->line} col {$error->column} {$error->message}";
                    continue;
                }

                try {
                    /**
                     * Finally, we'll insert the MJML before the nodeToInsertPromo.
                     */
                    $nodeToInsertPromo->parentNode->insertBefore($fragment, $nodeToInsertPromo);
                } catch (\Exception $e) {
                    Rollbar::log(Level::CRITICAL, $e->getMessage(), [
                        'errors' => $errors,
                        'mjml' => $test,
                    ]);
                }
            }
        }

        $body = $crawler->filter('body');

        libxml_clear_errors();

        /**
         * Now that we have constructed a new DOM, we will get the `body` HTML and return it.
         */
        $markup = $body->html();

        $responseBody = [
            'errors' => $errors,
            'markup' => $markup,
        ];

        $responseStatus = (empty($markup)
            ? 500
            : empty($errors))
                ? 200
                : 207;

        return new Response('', $responseStatus, $responseBody);
    }

    /**
     * The Quill Lexer is a Quill Delta parser, which will make life so much easier for us when we
     * want to translate our Letter editor super-object into html (or mjml).
     *
     * @param string $delta
     * @return Lexer
     */
    private function registerDeltaTransformationFormattersAndInstantiateLexer(string $delta): Lexer
    {
        $mjImageReplacementListener = new Image();
        $mjImageReplacementListener->wrapper = '<mj-image src="{src}"></mj-image>';
        $deltaToHtmlTransformer = new Lexer($delta);

        $deltaToHtmlTransformer->registerListener(new LetterDeltaParserTextFormatter());
        $deltaToHtmlTransformer->registerListener($mjImageReplacementListener);
        $deltaToHtmlTransformer->registerListener(new LetterDeltaParserHeadingFormatter());
        $deltaToHtmlTransformer->registerListener(new LetterDeltaParserSegmentSectionFormatter());
        $deltaToHtmlTransformer->registerListener(new LetterDeltaParserListFormatter());

        return $deltaToHtmlTransformer;
    }

    public function renderMjmlTemplate(UserCollection $authors, Channel $channel, string $delta, Letter $letter, array $promotions): Response
    {
        $arrayOfAuthorFullNames = $authors->getArrayOfUserFullNames();
        $byline = $this->generateLetterBylineFromArrayOfAuthors($arrayOfAuthorFullNames);

        $mjml = $this->generateMjmlFromDeltaAndPromotions($byline, $delta, $promotions, $channel->getLoadPromosBeforeHeadings(), $letter->getTitle(), $letter->getSubtitle());

        if ($mjml->isError()) {
            return $mjml;
        }

        $mjmlAfterFinalGarbageCollection = str_replace([
            '<mj-section><mj-column><mj-section',
        ], [
            '<mj-section',
        ], $mjml->getData());

        $letter->setMjmlTemplate($mjmlAfterFinalGarbageCollection);

        $bladeTemplateName = 'mjml';

        $letterBanner = empty($letter->getSpecialBanner())
        ? $channel->getChannelHorizontalLogo()
        : $letter->getSpecialBanner();

        $template = view($bladeTemplateName, [
            'newsletter' => $letter,
            'channel' => $channel,
            'banner' => $letterBanner
        ])->render();

        return new Response('', 200, $template);
    }

    private function transformDeltaToHtml(string $authors, string $delta, string $title, string $subtitle): Response
    {
        try {
            $transformer = $this->registerDeltaTransformationFormattersAndInstantiateLexer($delta);
            $mjmlTransformedFromDelta = $transformer->render();

            $titleAndSubtitleAndByline = "<mj-text css-class='heading heading--h1'><h1>{$title}</h1><p class='letter__subtitle'>{$subtitle}</p><p class='letter__byline'>By {$authors}</p></mj-text>";
            $headerAndContent = "{$titleAndSubtitleAndByline}{$mjmlTransformedFromDelta}";
            return new Response('', 200, $headerAndContent);
        } catch (\TypeError $e) {
            return new Response('We failed to generate html from the given delta', 500, $e);
        }
    }
}
