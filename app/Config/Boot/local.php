<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY - LOCAL
 |--------------------------------------------------------------------------
 | In local, show all errors for debugging
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode enabled for local development
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);
