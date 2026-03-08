<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY - STAGING
 |--------------------------------------------------------------------------
 | In staging, show errors for debugging but less verbose than development
 */
ini_set('display_errors', '1');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode enabled for staging
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);
