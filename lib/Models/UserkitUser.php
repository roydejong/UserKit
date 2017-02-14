<?php

namespace UserKit\Models;

use UserKit\Runtime\Model;

/**
 * A user record.
 *
 * @property    int             $id                 Userkit record id (primary key).
 * @property    string          $key                Unique ID to identify the user on the service.
 * @property    string          $name               Display name or nickname for the user.
 * @property    string          $email              The e-mail address for the user.
 * @property    \DateTime       $dt_first_seen      The first time this user was seen by UserKit.
 * @property    \DateTime       $dt_last_seen       The most recent time this user was seen by UserKit.
 *
 * @method static UserkitUser static find(int $id)
 * @method static UserkitUser find_by_key(string $key)
 * @method static UserkitUser[] all()
 */
class UserkitUser extends Model
{

}