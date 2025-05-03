<?php

namespace Helpers;

use \Bitrix\Main\Loader,
    \Bitrix\Highloadblock as HL;

class HlHelper
{

    public function __construct()
    {
        Loader::includeModule("highloadblock");
    }

    /**
     * Возвращение строкового названия класса сущности hl блока
     * @param int $hlId
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getEntityClass(int $hlId): string
    {
        if (!(int)$hlId) {
            return '';
        }

        $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}