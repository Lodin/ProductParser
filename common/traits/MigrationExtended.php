<?php

namespace common\traits;

trait MigrationExtended
{
    public function tinyint($display = 3)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder("tinyint($display)");
    }
    
    public function enum(array $list)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder("enum('".implode("','",$list)."')");
    }
}
