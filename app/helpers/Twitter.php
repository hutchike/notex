<?
// Copyright (c) 2009 Guanoo, Inc.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Lesser General Public License for more details.

class Twitter extends YAWF
{
    public static function screen_name($access_token)
    {
        return self::info($access_token, 'screen_name');
    }

    public static function user_id($access_token)
    {
        return self::info($access_token, 'user_id');
    }

    public static function oauth_token($access_token)
    {
        return self::info($access_token, 'oauth_token');
    }

    public static function oauth_token_secret($access_token)
    {
        return self::info($access_token, 'oauth_token_secret');
    }

    public static function info($access_token, $key)
    {
        return is_array($access_token) ? array_key($access_token, $key) : NULL;
    }
}

// End of Twitter.php
