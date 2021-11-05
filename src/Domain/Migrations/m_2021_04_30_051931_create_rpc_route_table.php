<?php

namespace Migrations;

use Illuminate\Database\Schema\Blueprint;
use ZnLib\Migration\Domain\Base\BaseCreateTableMigration;
use ZnLib\Migration\Domain\Enums\ForeignActionEnum;

class m_2021_04_30_051931_create_rpc_route_table extends BaseCreateTableMigration
{

    protected $tableName = 'rpc_route';
    protected $tableComment = 'Правила безопасности RPC-методов';

    public function tableSchema()
    {
        return function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('Идентификатор');
            $table->string('method_name')->comment('Имя RPC-метода');
            $table->string('version')->comment('Версия');
            $table->boolean('is_verify_eds')->comment('Верификация ЭЦП');
            $table->boolean('is_verify_auth')->comment('Верификация токена аутентификации');
            $table->string('permission_name')->comment('Верификация привилегий использования');
            $table->string('handler_class')->comment('Класс обработчика');
            $table->string('handler_method')->comment('Метод класса обрабочтка');
            //$table->integer('version_id')->comment('Версия с привязкой к конкретному классу');
            $table->integer('status_id')->comment('Статус');

            $table->unique(['method_name', 'version']);

            $table
                ->foreign('permission_name')
                ->references('name')
                ->on($this->encodeTableName('rbac_item'))
                ->onDelete(ForeignActionEnum::CASCADE)
                ->onUpdate(ForeignActionEnum::CASCADE);
            /*
            $table
                ->foreign('version_id')
                ->references('id')
                ->on($this->encodeTableName('security_version_handler'))
                ->onDelete(ForeignActionEnum::CASCADE)
                ->onUpdate(ForeignActionEnum::CASCADE);*/
        };
    }
}
