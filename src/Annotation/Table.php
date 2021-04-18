<?php

namespace App\Annotation;

/**
 * Headers for data that can be put in a table
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Table implements \JsonSerializable
{
    public $name;
    public $fieldType;
    public $path;
    public $enumName;

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'fieldType' => $this->fieldType,
            'path' => $this->path,
            'enumName' => $this->enumName
        ];
    }
}