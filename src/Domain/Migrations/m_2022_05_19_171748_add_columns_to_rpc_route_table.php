<?php

namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use ZnDatabase\Migration\Domain\Base\BaseColumnMigration;

class m_2022_05_19_171748_add_columns_to_rpc_route_table extends BaseColumnMigration
{
    protected $tableName = 'rpc_route';

    public function tableSchema()
    {
        return function (Blueprint $table) {
            $table->string('title')->nullable()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');
        };
    }
}