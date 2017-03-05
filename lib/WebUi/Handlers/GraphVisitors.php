<?php

namespace UserKit\WebUi\Handlers;

use ActiveRecord\Connection;
use ActiveRecord\DateTime;
use UserKit\Models\UserkitVisitor;
use UserKit\UserKit;
use UserKit\WebUi\Request;
use UserKit\WebUi\RequestHandler;
use UserKit\WebUi\WebUi;

/**
 * Fetches visitor data for graphing.
 */
class GraphVisitors extends RequestHandler
{
    /**
     * @inheritdoc
     */
    public function handle(Request $request): void
    {
        // TODO Fetch date parameters

        $fromDate = new DateTime();
        $fromDate->modify('-8 days');
        $fromDate->setTime(0, 0, 0);

        $toDate = new DateTime();
        $toDate->modify('-1 day');
        $toDate->setTime(23, 59, 59);

        $pdoParams = [$fromDate->format(Connection::$date_format), $toDate->format(Connection::$date_format)];
        /**
         * @var $pdoStatement \PDOStatement
         */
        $pdoStatement = UserkitVisitor::connection()->query('SELECT COUNT(fingerprint) AS `count`, date FROM userkit_visitors WHERE date >= ? AND date <= ? GROUP BY date ORDER BY date ASC;', $pdoParams);

        $dataAll = $pdoStatement->fetchAll();
        $dataIndexed = [];

        $dayAmount = $toDate->diff($fromDate)->days;
        $dayCounter = (clone $fromDate);

        for ($i = 0; $i < $dayAmount; $i++) {
            $dataIndexed[$dayCounter->format(Connection::$date_format)] = 0;
            $dayCounter->modify('+1 day');
        }

        foreach ($dataAll as $row) {
            $dataIndexed[$row['date']] = intval($row['count']);
        }

        $this->serveJsonResponse([
            'from' => $fromDate,
            'until' => $toDate,
            'data' => $dataIndexed
        ]);
    }
}