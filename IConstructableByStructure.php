<?php

namespace t35\Library;

interface IConstructableByStructure {
    /**
     * Получение экземпляра класса на основе структуры данных, описанной объектом специального класса.
     *
     * @see Structure
     * @param Structure $structure
     * @return static
     */
    public static function InstByStructure(Structure $structure): static;
}
