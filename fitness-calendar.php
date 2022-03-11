<?php
/*
  Plugin Name: Fitness Calendar
  Description: Fitness Calendar.
  Version: 1.3
  Author: Arslan Sarakaev
  Author URI: https://www.instagram.com/arslan_sarakaev/
  Plugin URI: https://kwork.ru/user/arslansarakaev
*/

include_once(__DIR__ . '/includes/functions.php');

register_activation_hook( __FILE__, 'fitness_calendar_activate' );

function fitness_calendar_activate(){
  update_option('fitness_works', []);
}

register_uninstall_hook( __FILE__, 'fitness_calendar_uninstall' );

function fitness_calendar_uninstall(){
  delete_option('fitness_works');
}