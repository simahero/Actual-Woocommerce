<?php

/**
 * Plugin Name:       Actual - Woocommerce
 * Description:       Actual Woocommerce Sync
 * Version:           1.0.2
 * Author:            Ront車 Zolt芍n
 * Author URI:        simahero.github.io
 * Text Domain:       actual-wp
 */

/* 
  01001001 00100000 01001100 01001111
  01010110 01000101 00100000 01011001
  01001111 01010101 00100000 01001100
  01001111 01010100 01010100 01001001
  00100000 00111100 00110011 00000000
*/

define('ROOT', __DIR__);

require_once('/vendor/autoload.php');
require_once('/src/Utils/Setup.php');

require_once('/src/OrderToActualXML.php');

new Setup();
