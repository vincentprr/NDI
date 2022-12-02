<?php

use Managers\NewsManager;
use Models\News;

require_once "Utils/requirements.php";

$pageTitle = "Accueil";
require_once $templatesPath . "head.html";
require_once $templatesPath . "header.html";
require_once $templatesPath . "home.html";

// $db
//     ->add(new News("Test", "Sample Text", fopen("imports/img/sis_logo_min.png", 'rb'), date("Y-m-d")))
//     ->commit();
