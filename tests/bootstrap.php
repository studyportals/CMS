<?php declare(strict_types=1);

use StudyPortals\CMS\ExceptionHandler;
use StudyPortals\Template\Template;

ExceptionHandler::enable();
Template::setTemplateCache('off');

error_reporting(E_ALL);
define('DEBUG_MODE', true);
