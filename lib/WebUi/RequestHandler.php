<?php

namespace UserKit\WebUi;


/**
 * A handler for a specific request type.
 */
abstract class RequestHandler
{
    /**
     * Handles an incoming request and returns a response.
     * This function SHOULD case output.
     * 
     * @param Request $request
     */
    public abstract function handle(Request $request): void;

    /**
     * Responds with a JSON payload.
     *
     * @param mixed $data
     */
    public function serveJsonResponse($data): void
    {
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($data);
    }
}