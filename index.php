<?php
/**
 * PHP Version 5 and above
 *
 * Demo index
 *
 * @category  PHP_Comment_Scripts
 * @package   PHP_Easy_Comments
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link      https://github.com/phhpro/easy-comments
 */


/**
 * Function ob_start() is required at the very top of every
 * page for which you want to enable comments to bypass the
 * "headers already sent" warning after posting.
 */
ob_start();


//** Header -- content above comments
require './header.php';


//** Configuration
require './config.php';


/**
 ***********************************************************************
 * Overrides                                                           *
 *                                                                     *
 * Parts of the configuration can be reset per page.                   *
 * These changes need to go here, between config.php and               *
 * comments.php. Refer to config.php for additional info.              *
 ***********************************************************************
 */


//** Main script
require './comments.php';


//** Footer -- content below comments
require './footer.php';
