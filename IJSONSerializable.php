<?php

namespace t35\Library;

/**
 * Интерфейс для объектов, которые могут реализоваться в json.
 */
interface IJSONSerializable {
    /**
     * Возвращает значение, пригодное для создания JSON-строки.
     *
     * @see json_encode()
     * @return string
     */
    public function JSONSerialize(): mixed;
}
