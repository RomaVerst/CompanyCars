<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("NIGHT_PILGRIM.COMPANY_CARS_NAME"),
    "DESCRIPTION" => Loc::getMessage("NIGHT_PILGRIM.COMPANY_CARS_COMPONENT_DESCRIPTION"),
    "ICON" => "/images/system.empty.png",
    "PATH" => [
        "ID" => "night-pilgrim",
        "SORT" => 100,
        "NAME" => Loc::getMessage("NIGHT_PILGRIM.COMPANY_CARS_COMPONENTS_FOLDER_NAME"),
    ],
    "CACHE_PATH" => "Y"
];

