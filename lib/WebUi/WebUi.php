<?php

namespace UserKit\WebUi;

use FlyPHP\Http\Request;
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
     * Begins processing WebUI requests.
     * This function causes output.
     */
    public function show(): void
    {
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        
        if ($requestMethod === 'POST') {
            
        } else if ($requestMethod === 'GET') {
            $view = new View('base.twig');
            $view->output();    
        }
    }
}