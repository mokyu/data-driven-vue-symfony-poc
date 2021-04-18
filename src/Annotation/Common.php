<?php

namespace App\Annotation;

/**
 * Common information about the class, what it's called and what actions are possible
 * @Annotation
 * @Target({"CLASS"})
 */
class Common implements \JsonSerializable
{
    public $tableTitle; // name of the schema
    public $formTitle;  // post url
    public $list;       // list endpoint
    public $post;       // post endpoint
    public $listKey;    // key used for showing schema in list
    public $listValue;  // value used for schema value

    public function jsonSerialize(): array
    {
        return [
            'tableTitle'    => $this->tableTitle ?? 'No table title given',
            'formTitle'     => $this->formTitle ?? 'No form title given',
            'list'          => $this->list,
            'post'          => $this->post,
            'listKey'       => $this->listKey,
            'listValue'     => $this->listValue
        ];
    }
}