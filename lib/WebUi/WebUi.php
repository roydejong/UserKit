<?php

namespace UserKit\WebUi;

use UserKit\Runtime\View;
use UserKit\WebUi\Handlers\AssetFetch;

/**
 * Provides the web based interface to view UserKit data.
 * 
 * The WebUI provides a embedded, integrated, zero-config user interface to UserKit.
 * It operates under the assumption that it is integrated into a user application on a specific endpoint.
 * 
 * When we receive a GET request, it renders the UI's base HTML. When we get a POST request, we dispatch it to a
 * request handler, if one is registered. This allows us to handle a bunch of request types, all on the same endpoint.
 */
class WebUi
{
    /**
     * Contains all registered request handlers, indexed by request type.
     *
     * @var RequestHandler[]
     */
    protected $handlers;

    /**
     * WebUi constructor.
     */
    public function __construct()
    {
        $this->bootstrap();
    }

    /**
     * Bootstraps the WebUI by registering a default set of request handlers.
     */
    protected function bootstrap(): void
    {
        $this->handlers = [];

        $this->handlers['asset.fetch'] = new AssetFetch();
    }

    /**
     * Extracts Request information based on the current PHP globals.
     *
     * @return Request
     */
    protected function parseRequest(): Request
    {
        $request = new Request();
        $request->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $request->payload = json_decode(file_get_contents('php://input'));
        $request->type = (isset($request->payload->type)) ? $request->payload->type : null;
        return $request;
    }

    /**
     * Begins processing WebUI requests.
     * This function causes output.
     */
    public function show(): void
    {
        $request = $this->parseRequest();

        switch ($request->requestMethod) {

            case 'POST':

                // Handle request with the registered handler, if available
                if (isset($this->handlers[$request->type])) {
                    $handler = $this->handlers[$request->type];
                    $handler->handle($request);
                    return;
                }

                break;

            case 'GET':

                // Initial page load
                $view = new View('base.twig');
                $view->output();
                return;
        }

        self::throwBadRequestError();
    }

    /**
     * Shows a bad request error page.
     */
    public static function throwBadRequestError(): void
    {
        header('HTTP/1.1 400 Bad Request');
        die("UserKit request error: Bad request.");
    }

    /**
     * Shows a bad request error page.
     */
    public static function throwNotFoundError(): void
    {
        header('HTTP/1.1 404 Not Found');
        die("UserKit request error: Target resource not found.");
    }
}