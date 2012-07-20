<?php
/*
Plugin Name: Sharingforce
Plugin URI: http://www.sharingforce.com/wordpress-plugin/
Description: A proven way to increase traffic and sharing on your web site. Sharingforce provides a great-looking sharing widget and optimizes Open Graph tags, but most importantly you have a simple way to offer rewards for sharing such as Sweepstakes, Gifts and Discounts! Works great on iPad as well as desktop computers.
Version: 2.1.2
Author: Sharingforce
Author URI: http://www.sharingforce.com/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	die('Not callable directly');
}

require_once(dirname(__FILE__) . '/php/Sharingforce.php');
Sharingforce::getInstance()->run();
