<?php
/*************************************************************************************
 * tcl.php
 * ---------------------------------
 * Author: Reid van Melle (rvanmelle@gmail.com)
 * Copyright: (c) 2004 Reid van Melle (sorry@nowhere)
 * Release Version: 1.0.7.14
 * CVS Revision Version: $Revision: 10127 $
 * Date Started: 2006/05/05
 * Last Modified: $Date: 2006-09-25 18:46:28 +1000 (Mon, 25 Sep 2006) $
 *
 * TCL/iTCL language file for GeSHi.
 *
 * This was thrown together in about an hour so I don't expect
 * really great things.  However, it is a good start.  I never
 * got a change to try out the iTCL or object-based support but
 * this is not widely used anyway.
 *
 * CHANGES
 * -------
 * 2006/05/05 (1.0.0)
 *  -  First Release
 *
 * TODO (updated 2006/05/05)
 * -------------------------
 * - Get TCL built-in special variables hilighted with a new color..
 *   currently, these are listed in //special variables in the keywords
 *   section, but they get covered by the general REGEXP for symbols
 * - General cleanup, testing, and verification
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = array (
	'LANG_NAME' => 'TCL',
	'COMMENT_SINGLE' => array(1 => '#'),
	'COMMENT_MULTI' => array(),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
	'QUOTEMARKS' => array('"', "'"),
	'ESCAPE_CHAR' => '\\',
	'KEYWORDS' => array(

		/*
		** Set 1: reserved words
		** http://python.org/doc/current/ref/keywords.html
		*/
		1 => array(
			'proc', 'global', 'upvar', 'if', 'then', 'else', 'elseif', 'for', 'foreach',
			'break', 'continue', 'while', 'set', 'eval', 'case', 'in', 'switch',
			'default', 'exit', 'error', 'proc', 'return', 'uplevel', 'loop', 
			'for_array_keys', 'for_recursive_glob', 'for_file', 'unwind_protect',
			'expr', 'catch', 'namespace', 'rename', 'variable',
			// itcl
			'method', 'itcl_class', 'public', 'protected'),

		/*
		** Set 2: builtins
		** http://asps.activatestate.com/ASPN/docs/ActiveTcl/8.4/tcl/tcl_2_contents.htm
		*/
		2 => array(
			// string handling
			'append', 'binary', 'format', 're_syntax', 'regexp', 'regsub',
			'scan', 'string', 'subst', 
			// list handling
			'concat', 'join', 'lappend', 'lindex', 'list', 'llength', 'lrange',
			'lreplace', 'lsearch', 'lset', 'lsort', 'split',
			// math
			'expr',
			// procedures and output
			'incr', 'close', 'eof', 'fblocked', 'fconfigure', 'fcopy', 'file',
			'fileevent', 'flush', 'gets', 'open', 'puts', 'read', 'seek', 
			'socket', 'tell',
			// packages and source files
			'load', 'loadTk', 'package', 'pgk::create', 'pgk_mkIndex', 'source',
			// interpreter routines
			'bgerror', 'history', 'info', 'interp', 'memory', 'unknown',
			// library routines
			'enconding', 'http', 'msgcat',
			// system related
			'cd', 'clock', 'exec', 'exit', 'glob', 'pid', 'pwd', 'time',
			// platform specified
			'dde', 'registry', 'resource',
			// special variables
			'$argc', '$argv', '$errorCode', '$errorInfo', '$argv0', 
			'$auto_index', '$auto_oldpath', '$auto_path', '$env', 
			'$tcl_interactive', '$tcl_libpath', '$tcl_library', 
			'$tcl_pkgPath', '$tcl_platform', '$tcl_precision', '$tcl_traceExec',
			),

		/*
		** Set 3: standard library
		*/
		3 => array(
			'comment', 'dde', 'filename', 'http', 'library', 'memory',
			'packagens', 'registry', 'resource', 'tcltest', 'tclvars',
			),

		/*
		** Set 4: special methods
		*/
		4 => array(
			)

		),
	'SYMBOLS' => array(
			'(', ')', '[', ']', '{', '}', '$', '*', '&', '%', '!', ';', '<', '>', '?'
		),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => true,
		1 => true,
        2 => true,
        3 => true,
        4 => true
		),
	'STYLES' => array(
		'KEYWORDS' => array(
			1 => 'color: #ff7700;font-weight:bold;',	// Reserved
			2 => 'color: #008000;',						// Built-ins + self
			3 => 'color: #dc143c;',						// Standard lib
			4 => 'color: #0000cd;'						// Special methods
			),
		'COMMENTS' => array(
			1 => 'color: #808080; font-style: italic;',
			'MULTI' => 'color: #808080; font-style: italic;'
			),
		'ESCAPE_CHAR' => array(
			0 => 'color: #000099; font-weight: bold;'
			),
		'BRACKETS' => array(
			0 => 'color: black;'
			),
		'STRINGS' => array(
			0 => 'color: #483d8b;'
			),
		'NUMBERS' => array(
			0 => 'color: #ff4500;'
			),
		'METHODS' => array(
			1 => 'color: black;'
			),
		'SYMBOLS' => array(
			0 => 'color: #66cc66;'
			),
		'REGEXPS' => array(
            0 => 'color: #ff3333;'
			),
		'SCRIPT' => array(
			)
		),
	'URLS' => array(
		),
	'OOLANG' => true,
	'OBJECT_SPLITTERS' => array(
		1 => '::'
		),
	'REGEXPS' => array(
		0 => '[\\$]+[a-zA-Z_][a-zA-Z0-9_]*',
		),
	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(
		),
	'HIGHLIGHT_STRICT_BLOCK' => array(
		)
);

?>
