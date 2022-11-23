<?php

namespace app\models;

use app\models\BaseModel;

class TagModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTags()
    {
        $query = 'SELECT * FROM tags';
        return $this->fetchAll($query);
    }
}