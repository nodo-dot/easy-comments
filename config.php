<?php
/**
 * PHP Version 5 and above
 *
 * Configuration
 *
 * @category  PHP_Comment_Scripts
 * @package   PHP_Easy_Comments
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/easy-comments
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */


/**
 * Document root -- "/your/path" without / if $_SERVER has wrong value
 * Script folder
 * Default directory index
 */
$eco_path = $_SERVER['DOCUMENT_ROOT'];
$eco_fold = "/easy-comments/";
$eco_dirx = "index.php";


/**
 * Mail account to send notifications
 * Mail account to receive notifications
 * Notification subject
 * Mail From header
 *
 * The mail sender must exist on the same host running the script.
 * The recipient can be any valid mail address. Default is sender.
 */
$eco_mafr = "info@" . $_SERVER['HTTP_HOST'];
$eco_mato = $eco_mafr;
$eco_msub = "PHP_Easy_Comments_NEW";
$eco_mhdr = "From: PHP_Easy_Comments &lt;$eco_mafr&gt;";


/**
 * Admin prefix and suffix
 *
 * Enter both values without spaces into the name field to post
 * as admin, e.g. "magicadmin" will publish your entry as "admin".
 *
 * Note that "admin" by itself is per defaut blocked to restrain
 * impersonators. Refer to restricted.php for more info.
 */
$eco_apfx = "magic";
$eco_asfx = "admin";


/**
 * Query string to list log
 * Date and time format
 *
 * The master log records every post ever made.
 * You can view it by appending the value of $eco_list
 * to the script's URL, e.g. /easy-comments/?list
 */
$eco_list = "list";
$eco_date = date('Y-m-d H:i:s');


/**
 ***********************************************************************
 * Any of the below settings can be overridden per page by             *
 * re-declaring values between including config.php and comments.php   *
 *                                                                     *
 * Default without overrides:                                          *
 *                                                                     *
 * require '/path/to/config.php';                                      *
 * require '/path/to/comments.php';                                    *
 *                                                                     *
 * Change maximum characters and disable permalinks:                   *
 *                                                                     *
 * require '/path/to/config.php';                                      *
 * $eco_pmax = 4096;                                                   *
 * $eco_perm = 0;                                                      *
 * require '/path/to/comments.php';                                    *
 *                                                                     *
 * Show existing comments but disable new entries:                     *
 *                                                                     *
 * require '/path/to/config.php';                                      *
 * $eco_anew = 0;                                                      *
 * require '/path/to/comments.php';                                    *
 ***********************************************************************
 */


/**
 * Maximum characters allowed per post
 *
 * You'll probably want to reduce the default rather than increase it.
 */
$eco_pmax = 1024;


/**
 ***********************************************************************
 * Below settings take values of 0 or 1 only, where 0 = NO and 1 = YES *
 ***********************************************************************
 */


/**
 * Print permalinks
 *
 * Adds a visual link of the comment ID to faciliate sharing.
 */
$eco_perm = 1;


/**
 * Accept Latin characters only
 *
 * Enable this to prevent users posting non-Latin characters,
 * e.g. Chinese, Russian, Arab, etc. The default is to allow 
 * any script, including Klingon :)
 */
$eco_plat = 0;


/**
 * Require moderator approval
 *
 * Enabling this requires $eco_note = 1;
 */
$eco_moda = 0;


/**
 * Send notifications for new comments
 *
 * Enable to send notification for new posts.
 * Depending how active your site is, this may produce A LOT of mail!
 */
$eco_note = 0;


/**
 * Require control code
 *
 * Adds a simple text CAPTCHA to limit SPAM.
 */
$eco_ctrl = 1;


/**
 * Allow new comments
 *
 * Disable to show existing comments but don't accept new entries.
 */
$eco_anew = 1;


/**
 * That's all folks. Happy commenting.
 */
