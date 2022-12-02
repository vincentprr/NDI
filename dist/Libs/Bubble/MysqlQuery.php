<?php
namespace BubbleORM;

use BubbleORM\DatabaseAccessor;
use BubbleORM\Exceptions\MissingColumnException;

class MysqlQuery
{
    private ?array $result;
    private array $conditions;
    private array $orderFuncs;
    private ?ModelInformations $modelInfos;

    public function __construct(
        private readonly DatabaseAccessor $db,
        private readonly string $class,
    ) {
        $this->result = null;
        $this->conditions = array();
        $this->orderFuncs = array();
        $this->properties = array();

        if(!ModelInformationsCache::tryGetModelInformations($this->class, $this->modelInfos))
            $this->modelInfos = ModelInformationsCache::tryRegisterModelInformations($this->class);
    }

    private function buildResult(mixed $raw) : void{
        if(!is_null($this->result)){
            $instance = ($this->modelInfos->instanceFactory)();

            foreach($this->modelInfos->properties as $property){
                if(array_key_exists($property->getDbName(), $raw))
                    $property->bindValue($instance, $raw[$property->getDbName()]);
                else
                    throw new MissingColumnException($property->getDbName(), $this->modelInfos->tableInfos->tableName);
            }
            
            if(count(array_filter($this->conditions, fn($func) => $func($instance))) == count($this->conditions))
                $this->result[] = $instance;

            if(!empty($this->result))
                foreach($this->orderFuncs as $orderFunc)
                    usort($this->result, fn($x) => $orderFunc($x) == true);
        }
    }

    public function where(callable $func) : self{
        $this->conditions[] = $func;
        
        if($this->result != null) {
            $newResult = array();

            foreach($this->result as $res)
                if($func($res))
                    $newResult[] = $res;
            
            $this->result = $newResult;
        }

        return $this;
    }

    public function orderBy(callable $orderFunc) : self{
        $this->orderFuncs[] = $orderFunc;

        if($this->result != null)
            usort($this->result, fn($x) => $orderFunc($x) == true);

        return $this;
    }

    public function all() : array{
        if(is_null($this->result)){
            $this->result = array();

            foreach($this->db->fetchAll("SELECT * FROM ". $this->modelInfos->tableInfos->tableName) as $raw){
                $this->buildResult($raw);
            }
        }

        return $this->result;
    }

    public function firstOrDefault() : mixed{
        if(is_null($this->result)){
            $this->result = array();

            foreach($this->db->fetchAll("SELECT * FROM ". $this->modelInfos->tableInfos->tableName) as $raw){
                $this->buildResult($raw);

                if($this->result != null)
                    return $this->result[0];
            }
        }

        return null;
    }

    public function clearCache() : self{
        $this->result = null;

        return $this;
    }

    public function clearWhereClauses() : self{
        $this->conditions = array();

        return $this;
    }

    public function dispose() : self{
        $this->clearCache();

        return $this->clearWhereClauses();
    }
}