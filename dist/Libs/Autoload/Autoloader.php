<?php
$baseNamespaces = [
    "BubbleORM" => "Libs/Bubble/",
    "Models" => "imports/Models/",
    "Managers" => "imports/Managers/"
];

spl_autoload_register('autoload');

/**
 * Inclue le fichier correspondant à notre classe
 * @param $class string Le nom de la classe à charger
 */
function autoload($class) : void{
    global $baseNamespaces;

    $splittedNamespace = explode('\\', $class);
    
    require_once (array_key_exists($splittedNamespace[0], $baseNamespaces) ? $baseNamespaces[$splittedNamespace[0]] : "") . join('\\', array_slice($splittedNamespace, 1)) .'.php';
}