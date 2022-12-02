<?php

use BubbleORM\DatabaseAccessor;

$siteName = "SIS Association"; // Name of the site

$dbHost = "127.0.0.1";
$dbUser = "root";
$dbPassword = "";
$dbName = "ndi";

/* DO NOT MODIFIED */
// DATABASE
$db = new DatabaseAccessor($dbHost, $dbUser, $dbPassword, $dbName);

// PATHS
$imgPath = "./Imports/img/"; // Path to images
$cssPath = "./Imports/css/"; // Path to css files
$templatesPath = __DIR__ ."/../Imports/templates/"; // Path to templates