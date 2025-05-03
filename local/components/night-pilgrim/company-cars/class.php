<?php

use \Bitrix\Main\UserTable,
    \Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\DateTime,
    \Helpers\HlHelper;

class CarReservationComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            ShowError(Loc::getMessage('NIGHT_PILGRIM.CLASS_COMPANY_CARS_NOT_AUTH'));
            return;
        }

        if (empty($this->arParams['AUTO_HL_BLOCK'])
            || empty($this->arParams['COMFORT_HL_BLOCK'])
            || empty($this->arParams['DRIVERS_HL_BLOCK'])
            || empty($this->arParams['EMPLOYEE_POSITIONS_HL_BLOCK'])
            || empty($this->arParams['RESERVATIONS_HL_BLOCK'])
        ) {
            ShowError(Loc::getMessage('NIGHT_PILGRIM.CLASS_COMPANY_CARS_HL_BLOCK_EMPTY'));
            return;
        }

        $start = (int)htmlspecialchars($_GET['start']);
        $end = (int)htmlspecialchars($_GET['end']);
        $hlHelper = new HlHelper();

        //выбираем должность пользователя
        $userInfo = UserTable::getList([
            'filter' => ['ID' => $USER->GetID()],
            'select' => ['ID', 'UF_POSITION'],
            'cache' => ['ttl' => 3600],
        ])->fetch();

        //создаём все нужные классы hl блоков
        $positionsHlClass = $hlHelper->getEntityClass((int)$this->arParams['EMPLOYEE_POSITIONS_HL_BLOCK']);
        $comfortCategoryHlClass = $hlHelper->getEntityClass((int)$this->arParams['COMFORT_HL_BLOCK']);
        $carsHlClass = $hlHelper->getEntityClass((int)$this->arParams['AUTO_HL_BLOCK']);
        $reservationHlClass = $hlHelper->getEntityClass((int)$this->arParams['RESERVATIONS_HL_BLOCK']);
        $driversHlClass = $hlHelper->getEntityClass((int)$this->arParams['DRIVERS_HL_BLOCK']);

        //выбираем категории комфорта для текущей должности
        $infoPrivilege = $positionsHlClass::getList([
            'filter' => ['ID' => $userInfo['UF_POSITION']],
            'select' => ['ID', 'UF_COMFORT_CATEGORY']
        ])->fetch();

        if (empty($infoPrivilege['UF_COMFORT_CATEGORY'])) {
            ShowError(Loc::getMessage('NIGHT_PILGRIM.CLASS_COMPANY_CARS_EMPTY_PRIVILEGE'));
            return;
        }

        //делаем выборку всех автомобилей найденных категорий комфорта
        $cars = [];

        $carsDb = $carsHlClass::getList([
            'filter' => [
                'UF_COMFORT_CATEGORY' => $infoPrivilege['UF_COMFORT_CATEGORY']
            ],
            'runtime' => [
                new \Bitrix\Main\Entity\ReferenceField(
                    'DRIVER',
                    $driversHlClass,
                    ['=this.UF_DRIVER' => 'ref.ID'],
                    ['join_type' => 'inner']
                ),
                new \Bitrix\Main\Entity\ReferenceField(
                    'COMFORT_CATEGORY',
                    $comfortCategoryHlClass,
                    ['=this.UF_COMFORT_CATEGORY' => 'ref.ID'],
                    ['join_type' => 'inner']
                ),
            ],
            'cache' => ['ttl' => 3600],
            'select' => ['ID', 'UF_NAME', 'DRIVER.UF_NAME', 'DRIVER.UF_LAST_NAME', 'COMFORT_CATEGORY.UF_NAME']
        ]);

        while ($car = $carsDb->fetch()) {
            $cars[$car['ID']] = $car;
        }


        if (!$start || !$end) {
            ShowError(Loc::getMessage('NIGHT_PILGRIM.CLASS_COMPANY_CARS_NO_ISSET_TIME'));
            return;
        }

        $objDateTimeStart = DateTime::createFromTimestamp($start);
        $objDateTimeEnd = DateTime::createFromTimestamp($end);

        //создаём массив занятных в заданное время автомобилей
        $busyCars = [];
        $reservationResult = $reservationHlClass::getList([
            'filter' => [
                'LOGIC' => 'OR',
                [
                    '<=UF_DATE_FROM' => $objDateTimeEnd->format('d.m.Y H:i:s'),
                    '>=UF_DATE_TO' => $objDateTimeStart->format('d.m.Y H:i:s'),
                ],
            ],
            'select' => ['UF_CAR_ID']
        ]);

        while ($res = $reservationResult->fetch()) {
            if (!in_array($res['UF_CAR_ID'], $busyCars)) {
                $busyCars[] = $res['UF_CAR_ID'];
            }
        }

        //удаление из массива автомобилей занятые авто
        if (!empty($busyCars)) {
            foreach ($cars as $idCar => $car) {
                if (in_array($idCar, $busyCars)) {
                    unset($cars[$idCar]);
                }
            }
        }

        $this->arResult['ITEMS'] = $cars;

        $this->includeComponentTemplate();
    }
}
