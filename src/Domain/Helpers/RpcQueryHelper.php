<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Domain\Query\Entities\Query;

class RpcQueryHelper
{

    public static function query2RpcParams(Query $query): array {
        $params = [];
        if($query->getWhere()) {
            $filter = [];
            foreach ($query->getWhere() as $where) {
                $filter[$where->column] = $where->value;
            }
            $params['filter'] = $filter;
        }
        if($query->getPage()) {
            $params['page'] = $query->getPage();
        }
        if($query->getPerPage()) {
            $params['perPage'] = $query->getPerPage();
        }
        return $params;
    }
}
