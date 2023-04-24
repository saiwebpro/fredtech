<?php

namespace spark\models;

/**
* EngineModel
*
*/
class EngineModel extends Model
{
    protected static $table = 'engines';

    protected $queryKey = 'engine_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'          => ['created_at' => 'DESC'],
        'oldest'          => ['created_at' => 'ASC'],
        'a2z'             => ['engine_name'  => 'ASC'],
        'z2a'             => ['engine_name'  => 'DESC'],
    ];
}
