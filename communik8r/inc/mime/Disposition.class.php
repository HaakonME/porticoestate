<?php

/**
 * Disposition.class.php
 *
 * Copyright (c) 2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This contains functions needed to handle mime messages.
 *
 * $Id: Disposition.class.php,v 1.1.1.1 2005/08/23 05:04:02 skwashd Exp $
 * @package squirrelmail
 */

/**
 * Undocumented class
 * @package squirrelmail
 */
class Disposition {
    function Disposition($name) {
       $this->name = $name;
       $this->properties = array();
    }

    function getProperty($par) {
        $value = strtolower($par);
        if (isset($this->properties[$par])) {
            return $this->properties[$par];
        }
        return '';
    }
}

?>
