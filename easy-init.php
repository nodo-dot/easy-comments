<?php
/**
 * PHP Version 5 and above
 *
 * Easy Comments is a trivial comments script with minimal bloat.
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link     https://github.com/phhpro/easy-comments
 *
 *
 * The content of this file MUST go at the very top of every page for
 * which you want to enable comments. ob_start() is required to bypass
 * the "headers already sent" warning after posting.
 */
session_start();
ob_start();
