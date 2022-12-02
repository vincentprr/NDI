<?php
namespace BubbleORM;

use PDO;
use PDOStatement;

class DatabaseAccessor{
    private array $items;
    private array $itemsToDelete;
    private readonly PDO $db;

    public function __construct(string $host, string $user, string $password, string $dbName) {
        $this->items = array();
        $this->itemsToDelete = array();

        $this->db = new PDO("mysql:host=$host;dbname=$dbName", $user, $password);
    }

    private function clearCache() : void{
        $this->items = array();
        $this->itemsToDelete = array();
    }

    public function execute(string $request, array $params = []) : PDOStatement{
        $req = $this->db->prepare($request);
        $req->execute($params);

        return $req;
    }

    public function fetch(string $request, array $params = []) : mixed{
        return $this->execute($request, $params)->fetch();
    }

    public function fetchAll(string $request, array $params = []) : array{
        return $this->execute($request, $params)->fetchAll();
    }

    public function createQuery(string $className) : MysqlQuery{
        return new MysqlQuery($this, $className);
    }

    public function add(object $item) : self{
        ModelInformationsCache::tryRegisterModelInformations($item::class);
        $this->items[] = $item;

        return $this;
    }

    public function addRange(array $items) : self{
        foreach($items as $item)
            $this->add($item);

        return $this;
    }

    public function remove(object $item) : self{
        ModelInformationsCache::tryRegisterModelInformations($item::class);
        $this->itemsToDelete[] = $item;

        return $this;
    }

    public function removeRange(array $items) : self{
        foreach($items as $item)
            $this->remove($item);

        return $this;
    }

    public function commit() : void{
        $request = "";

        foreach($this->itemsToDelete as $item){
            $modelInformations = null;

            if(ModelInformationsCache::tryGetModelInformations($item::class, $modelInformations)){
                $request .= "DELETE FROM ". $modelInformations->tableInfos->tableName ." WHERE ";
                $request .= implode(" AND ", array_map(fn($key) => $key->getDbName() ." = ". (is_null(($value = $key->getValue($item))) ? "NULL" : "'". $value ."'"),
                    array_filter($modelInformations->properties, fn($dbClmn) => $dbClmn->isPrimaryKey()))) .";";
            }
        }

        foreach($this->items as $item){
            $modelInformations = null;

            if(ModelInformationsCache::tryGetModelInformations($item::class, $modelInformations)){
                $initializedProperties = array_filter($modelInformations->properties, fn($property) => $property->isInitialized($item));
                $keys = array_filter($initializedProperties, fn($dbClmn) => $dbClmn->isPrimaryKey());

                if(!empty($keys) && count(array_filter($keys, fn($key) => $key->isInitialized($item))) == count($keys) && 
                    !is_null($this->fetch("SELECT 1 FROM ". $modelInformations->tableInfos->tableName ." WHERE ".
                        implode(" AND ", array_map(fn($key) => $key->getDbName() ." = ". $key->getValue($item), $keys)))))
                {
                    $request .= "UPDATE ". $modelInformations->tableInfos->tableName ." SET ".
                        implode(", ", array_map(fn($property) => $property->getDbName() ." = ". (is_null(($value = $property->getValue($item))) ? "NULL" : "'". $value ."'"),
                            $initializedProperties));
                    $request .= " WHERE ". implode(" AND ", array_map(fn($key) => $key->getDbName() ." = ". (is_null(($value = $key->getValue($item))) ? "NULL" : "'". $value ."'"),
                        $keys)) .";";
                }
                else{

                    if(!empty($initializedProperties)){
                        $request .= "INSERT INTO ". $modelInformations->tableInfos->tableName ." (";
                        $request .= implode(", ", array_map(fn($property) => $property->getDbName(), $initializedProperties)) .") VALUES (";
                        $request .= implode(", ", array_map(fn($property) => (is_null(($value = $property->getValue($item))) ? "NULL" : "'". $value ."'"), $initializedProperties)) .");";
                    }
                }
            }
        }

        $this->clearCache();
        $this->execute($request);
    }
}