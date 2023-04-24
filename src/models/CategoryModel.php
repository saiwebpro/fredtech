<?php

namespace spark\models;

/**
* CategoryModel
*
*/
class CategoryModel extends Model
{
    protected static $table = 'categories';

    protected $queryKey = 'category_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'                    => ['created_at' => 'DESC'],
        'oldest'                    => ['created_at' => 'ASC'],
        'a2z'                       => ['category_name'  => 'ASC'],
        'z2a'                       => ['category_name'  => 'DESC'],
        'category-order'            => ['category_order'  => 'ASC'],
        'category-order-descending' => ['category_order'  => 'DESC'],
    ];
}
