<?php

namespace UserKit\Models;

use ActiveRecord\DateTime;
use UserKit\Runtime\Model;

/**
 * A visitor log.
 *
 * @property    int             $id                     Userkit record id (primary key).
 * @property    string          $fingerprint            Visitor fingerprint, unique per date.
 * @property    DateTime        $date                   Date on which this visit occured.
 * @property    int             $page_views             The amount of pages viewed by this visitor on this day.
 * @property    string          $agent_browser          Browser name as specified by the user agent.
 * @property    string          $agent_platform         Platform / OS name as specified by the user agent.
 * @property    ?string         $referer                The referer URL, if applicable.
 * @property    string          $remote_address         Remote IP. Can be IPv4 or IPv6.
 *
 * @method static UserkitVisitor static find(int $id)
 * @method static UserkitVisitor find_by_fingerprint_and_date(string $fingerprint, string $date)
 * @method static UserkitVisitor[] all()
 */
class UserkitVisitor extends Model
{

}