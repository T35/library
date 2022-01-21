<?php

namespace t35\Library\Structure;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListCallables;
use t35\Library\BaseClass;
use t35\Library\EInclusionStatus;

abstract class NodeElement extends BaseClass {
    public function __construct(
        protected EInclusionStatus $inclusionStatus = EInclusionStatus::WhiteList,
        protected ListCallables    $listCallables = new ListCallables(),
        protected ?Structure       $nestedStructure = null
    ) {

    }

    /**
     * Создание экземпляра узла(node) структуры в зависимости от типа узла.
     *
     * @param ENodeType $nodeType Тип узла(node).
     * @param EInclusionStatus $inclusionStatus
     * @param ListCallables $listCallables
     * @param Structure|null $nestedStructure Вложенная структура.
     * @return static
     *@see Structure, ENodeType
     */
    public static function Instantiate(
        ENodeType        $nodeType,
        EInclusionStatus $inclusionStatus = EInclusionStatus::WhiteList,
        ListCallables    $listCallables = new ListCallables(),
        ?Structure       $nestedStructure = null
    ): static {
        return match ($nodeType) {
            ENodeType::Array => new NodeElementArray(
                $inclusionStatus,
                $listCallables,
                $nestedStructure
            ),
            ENodeType::Fields => new NodeElementFields(
                $inclusionStatus,
                $listCallables,
                $nestedStructure
            ),
            //ENodeType::Value
            default => new NodeElementValue(
                $inclusionStatus,
                $listCallables,
                $nestedStructure
            ),
        };
    }

    public abstract function GenerateFromData(ArrayBase &$newNodeData, mixed $nodeData, string $field_name): void;
}
