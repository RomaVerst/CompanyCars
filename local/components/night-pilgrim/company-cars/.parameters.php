<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Loader,
    \Bitrix\Highloadblock\HighloadBlockTable;

Loader::includeModule('highloadblock');
$hlBlocks = [];
$dbHlBlock = HighloadBlockTable::getList([
    'select' => ['ID', 'NAME', 'TABLE_NAME', 'LANG'],
    'order' => ['ID' => 'ASC']
]);
$all = [];
while ($hlblock = $dbHlBlock->fetch()) {
    $hlBlocks[$hlblock['ID']] = '[' . $hlblock['ID'] . '] ' . $hlblock['HIGHLOADBLOCK_HIGHLOAD_BLOCK_LANG_NAME'];
}

$arComponentParameters = [
    'PARAMETERS' => [
        'AUTO_HL_BLOCK' => [
            'NAME' => Loc::getMessage('NIGHT_PILGRIM.PARAMETERS_COMPANY_CARS_AUTO_HL_BLOCK'),
            'TYPE' => 'LIST',
            'VALUES' => $hlBlocks,
        ],
        'DRIVERS_HL_BLOCK' => [
            'NAME' => Loc::getMessage('NIGHT_PILGRIM.PARAMETERS_COMPANY_CARS_DRIVERS_HL_BLOCK'),
            'TYPE' => 'LIST',
            'VALUES' => $hlBlocks,
        ],
        'COMFORT_HL_BLOCK' => [
            'NAME' => Loc::getMessage('NIGHT_PILGRIM.PARAMETERS_COMPANY_CARS_COMFORT_HL_BLOCK'),
            'TYPE' => 'LIST',
            'VALUES' => $hlBlocks,
        ],
        'EMPLOYEE_POSITIONS_HL_BLOCK' => [
            'NAME' => Loc::getMessage('NIGHT_PILGRIM.PARAMETERS_COMPANY_CARS_EMPLOYEE_POSITIONS_HL_BLOCK'),
            'TYPE' => 'LIST',
            'VALUES' => $hlBlocks,
        ],
        'RESERVATIONS_HL_BLOCK' => [
            'NAME' => Loc::getMessage('NIGHT_PILGRIM.PARAMETERS_COMPANY_CARS_RESERVATIONS_HL_BLOCK'),
            'TYPE' => 'LIST',
            'VALUES' => $hlBlocks,
        ],
    ],
];
