<?php
namespace BubbleORM;

use BubbleORM\Attributes\Name;
use BubbleORM\Attributes\DefaultValue;
use BubbleORM\Attributes\Key;
use BubbleORM\Attributes\Unsigned;
use BubbleORM\Exceptions\IncorrectDefaultValueTypeException;
use BubbleORM\Exceptions\NotInitializedProperty;
use ReflectionProperty;
use ReflectionType;

class DatabaseColumn{
    private ReflectionType $type;
    private string $name;
    private string $dbName;
    private mixed $defaultValue;
    private bool $isPrimaryKey;
    private bool $isNullable;
    private bool $isUnsigned;

    private static array $typeRelation = [
        "int" => "integer",
        "float" => "double",
        "double" => "double",
        "bool" => "boolean",
        "string" => "string"
    ];

    public function __construct(
        private readonly ReflectionProperty $propertyInfos,
        array $attributes
    )
    {
        $this->type = $propertyInfos->getType();

        $this->name = $this->propertyInfos->getName();
        $this->dbName = $this->name;
        $this->isPrimaryKey = false;
        $this->isNullable = $this->type->allowsNull();
        $this->isUnsigned = false;
        $this->defaultValue = null;

        foreach($attributes as $attribute){
            switch($attribute->getName()){
                case Name::class:
                    $this->dbName = $attribute->newInstance()->name;
                    break;
                    
                case DefaultValue::class:
                    $this->defaultValue = $attribute->newInstance()->defaultValue;
                    break;
                
                case Key::class:
                    $this->isPrimaryKey = true;
                    break;

                case Unsigned::class:
                    $this->isUnsigned = true;
                    break;
            }
        }

        if(!is_null($this->defaultValue) && self::$typeRelation[$this->type->getName()] !== gettype($this->defaultValue))
            throw new IncorrectDefaultValueTypeException($this->name);
    }

    public function getDbName() : string{
        return $this->dbName;
    }

    public function isPrimaryKey() : bool{
        return $this->isPrimaryKey;
    }

    public function bindValue(object $instance, mixed $value) : void{
        $this->propertyInfos->setValue($instance, $value);
    }

    public function isInitialized($instance) : bool{
        return $this->propertyInfos->isInitialized($instance);
    }

    public function getValue(object $instance) : mixed{
        if(!$this->isInitialized($instance))
            throw new NotInitializedProperty($this->name);
        
        return $this->propertyInfos->getValue($instance);
    }
}