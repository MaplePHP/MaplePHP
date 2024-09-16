<?php

namespace Http\Controllers;

use MaplePHP\Http\Interfaces\ResponseInterface;
use MaplePHP\Foundation\Http\Provider;

/**
 * @method local(string $string)
 * @method head()
 * @method view()
 */
class HttpRequestError extends BaseController
{
    public function __construct(Provider $provider)
    {
    }

    /**
     * Handle 404, 403, ... Errors
     * @Route[*:[/{any:.*}]]
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function handleError(ResponseInterface $response): ResponseInterface
    {
        $response = $response->withStatus(404);
        $title = $this->local("auth")->get(("httpStatus" . $response->getStatusCode()), $response->getReasonPhrase());

        // Overwrite meta data
        $this->head()->getElement("title")->setValue($title);
        $this->head()->getElement("description")->attr("content", $title);

        // This partial named "httpStatus" will auto attach itself to the View
        $this->view()->setPartial("httpStatus", [
            "headline" => $title,
            "content" => $this->local("auth")->get(("httpStatusContent"), $response->getReasonPhrase(), [$title])
        ]);

        return $response;
    }
}
