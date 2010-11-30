<?php
// Copyright (c) 2010 Guanoo, Inc.
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

// Run YAWF!

chdir('../..');
ini_set('include_path', 'app:yawf:.');
require_once('lib/utils.php');
YAWF::respond_to_web_request();

// End of index.php
