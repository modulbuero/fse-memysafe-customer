<?php 
/**
 * Load function Files
 */
$theme_directory = 'functions/';
require_once($theme_directory.'wp/enqueue-scripts.php');
require_once($theme_directory.'functions.php');
require_once($theme_directory.'html_functions.php');
require_once($theme_directory.'login/redirect-nonuser.php');
require_once($theme_directory.'backend/backend-noaccess.php');
require_once($theme_directory.'login/logout.php');
require_once($theme_directory.'MemyUserDataEditor.php');
require_once($theme_directory.'MemyOptionManager.php');
require_once($theme_directory.'MemyTotmanschalter.php');
require_once($theme_directory.'MemyTimerMail.php');
require_once($theme_directory.'MemyContacts.php');
require_once($theme_directory.'MemyContactsVertreter.php');
require_once($theme_directory.'MemyContactsKunden.php');
require_once($theme_directory.'MemyProjectsManager.php');
require_once($theme_directory.'MemySafeUpload.php');
require_once($theme_directory.'MemySafeUploadAJAX.php');
require_once($theme_directory.'MemyFirstSettings.php');
require_once($theme_directory.'MemyContactHelperModus.php');
require_once($theme_directory.'cron/exam-clock-cron.php');
require_once($theme_directory.'MemyProtocolManager.php');
