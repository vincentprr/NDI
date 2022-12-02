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
function autoload(string $className) : void{
    global $baseNamespaces;

    $result = $className;

    foreach($baseNamespaces as $nameSpace => $path){
        $classPath = preg_replace("/^$nameSpace/", $path, $className);
        if($classPath != $result){
            $result = $classPath;
            break;
        }
    }
    
    require_once "$result.php";
}