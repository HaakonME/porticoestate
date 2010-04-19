<?php

/**
 * ContentType.class.php
 *
 * Copyright (c) 2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions needed to handle mime messages.
 *
 * $Id: ContentType.class.php,v 1.1.1.1 2005/08/23 05:04:04 skwashd Exp $
 * @package squirrelmail
 */

/**
 * Undocumented class
 * @package squirrelmail
 */
class ContentType {
    var $type0      = 'text',
        $type1      = 'plain',
        $properties = '';

    function ContentType($type) {
        $pos = strpos($type, '/');
        if ($pos > 0) {
            $this->type0 = substr($type, 0, $pos);
            $this->type1 = substr($type, $pos+1);
        } else {
            $this->type0 = $type;
        }
        $this->properties = array();
    }
}

?>
