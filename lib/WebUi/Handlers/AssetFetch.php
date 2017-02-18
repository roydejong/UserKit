<?php

namespace UserKit\WebUi\Handlers;

use UserKit\UserKit;
use UserKit\WebUi\Request;
use UserKit\WebUi\RequestHandler;
use UserKit\WebUi\WebUi;

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
        $requestedFile = $request->getValue('target');

        // Verify that a file path was requested, and it doesn't look like some kind of traversal attack
        if (!$requestedFile || empty($requestedFile) || strpos($requestedFile, '..') !== false ||
            strpos($requestedFile, '\\') !== false
        ) {
            WebUi::throwBadRequestError();
            return;
        }

        // Check the estimated true file path, and see if we can access it
        $sourcePath = UserKit::getLibraryPath() . "/assets/{$requestedFile}";

        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            WebUi::throwNotFoundError();
            return;
        }

        // Output
        $mimeType = mime_content_type($sourcePath);

        header('HTTP/1.1 200 OK');
        header("Content-Type: {$mimeType}");

        die(file_get_contents($sourcePath));
    }
}