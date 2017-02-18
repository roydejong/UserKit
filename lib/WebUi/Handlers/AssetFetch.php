<?php

namespace UserKit\WebUi\Handlers;

use UserKit\WebUi\Request;
use UserKit\WebUi\RequestHandler;

/**
 * Request handler for fetching static assets.
 */
class AssetFetch extends RequestHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request): void
    {
        echo 'hi!';
    }
}