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

    public function getEntityClass($hlId)
    {
        if (!(int)$hlId) {
            return '';
        }

        $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}