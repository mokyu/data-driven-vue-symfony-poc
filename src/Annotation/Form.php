<?php

namespace App\Annotation;

/**
 * field information for forms
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Form implements \JsonSerializable
{
    public $rules;
    public $name;
    public $path;
    public $placeholder;
    public $fieldType;
    public $dataSource;

    public function jsonSerialize()
    {
        return [
            'rules' => $this->rules,
            'fieldType' => $this->fieldType,
            'placeholder' => $this->placeholder,
            'name' => $this->name,
            'path' => $this->path,
            'dataSource' => $this->dataSource,
        ];
    }
}