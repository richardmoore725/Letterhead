<?php

namespace App\Http\Repositories;

use App\Http\Response;
use App\Models\PassportStamp;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

/**
 * The MjmlTemplateRepository connects PlatformService to an internal MJML-rendering server
 * we call, for lack of creativity, "Template Service."
 *
 * Class MjmlTemplateRepository
 * @package App\Http\Repositories
 */
class MjmlTemplateRepository extends BeaconRepository implements MjmlTemplateRepositoryInterface
{
    /**
     * Use `getHtmlFromMjml` to have our TemplateService return email-friendly markup
     * from a string of MJML.
     *
     * @param string $mjml
     * @return Response
     */
    public function getHtmlFromMjml(string $mjml): Response
    {
        $endpoint = "{$this->getEndpoint()}/render";
        $requestBody = [
            'mjml' => $mjml,
        ];

        $response = $this->getResponseFromApi($endpoint, '', 'POST', $requestBody);

        if ($response->isError()) {
            /**
             * If the response isn't good, our MJML Service returns an `errors` array that we
             * may find useful to send up the chain.
             */
            $responseData = $response->getData();

            /**
             * @var array
             */
            $errors = isset($responseData->errors) ? $responseData->errors : [];
            $errorFirstMessage = empty($errors) ? '' : $errors[0]->message;

            /**
             * We are going to assume Bad Request.
             */
            Rollbar::log(Level::ERROR, $errorFirstMessage);
            return new Response(
                'We apologize, an error occured when we were generating html template for your newsletter. We\'ve sounded the alarms internally and we\'ll address the bug. In the meantime, if you need help, ping us in chat and we\'ll help you finish your task.',
                400,
                $errors
            );
        }

        return $response;
    }

    private function getEndpoint(): string
    {
        $endpoint = env('SERVICE_MJML_ENDPOINT', '');
        return $endpoint;
    }
}
