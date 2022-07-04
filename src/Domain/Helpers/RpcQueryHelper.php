<?php

namespace ZnLib\Rpc\Domain\Helpers;

use Illuminate\Database\Query\Builder;
use ZnCore\Domain\Query\Entities\Join;
use ZnCore\Domain\Query\Entities\Query;

class RpcQueryHelper
{

    public static function query2RpcParams(Query $query): array {
        $params = [];
        self::setWhere($query, $params);
        self::setJoin($query, $params);
        self::setPaginate($query, $params);
        return $params;
    }

    public static function setWhere(Query $query, array &$params)
    {
        if($query->getWhere()) {
            $filter = [];
            foreach ($query->getWhere() as $where) {
                $filter[$where->column] = $where->value;
            }
            $params['filter'] = $filter;
        }
    }

    public static function setJoin(Query $query, array &$params)
    {
        if($query->getWith()) {
            $params['with'] = $query->getWith();
        }
    }

    public static function setPaginate(Query $query, array &$params)
    {
        if($query->getPage()) {
            $params['page'] = $query->getPage();
        }
        if($query->getPerPage()) {
            $params['perPage'] = $query->getPerPage();
        }
    }
}
