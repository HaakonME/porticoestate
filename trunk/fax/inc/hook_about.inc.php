<?php
 /**************************************************************************\
 * phpGroupWare - fax                                                       *
 * http://www.phpgroupware.org                                              *
 * This application written by:                                             *
 *                             Marco Andriolo-Stagno <stagno@prosa.it>      *
 *                             PROSA <http://www.prosa.it>                  *
 * -------------------------------------------------------------------------*
 * Funding for this program was provided by http://www.seeweb.com           *
 * -------------------------------------------------------------------------*
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id: hook_about.inc.php,v 1.2 2002/11/27 15:50:40 stagno Exp $ */

function about_app($tpl,$handle)
{
	return 'FAX module by <a href="http://www.prosa.it">PROSA</a> for <a href="http://www.seeweb.it">Seeweb</a>';
}
?>
