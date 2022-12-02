<?php
namespace BubbleORM;

use Closure;
use ReflectionClass;
use BubbleORM\Attributes\Table;

class ModelInformations{
    public function __construct(
        public readonly Closure $instanceFactory,
        public readonly array $properties,
        public readonly Table $tableInfos
    ){}
}