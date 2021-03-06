<?php

return array(
    array(
        array(
            array(
                'id' => '',
                'dateTransaction' => new \DateTime('2029-12-25'),
                'dateCreated'     => new \DateTime('2029-12-25 23:59:10'),
                'amount'          => 12345,
                'currency'        => 'EUR',
                'currencyName'    => 'Euro',
                'currencySymbol'  => '€',
                'email'           => 'email@email.com',
                'role'            => 'user',
                'userId'          => 123,
                'userName'        => 'Jane Doe',
                'itemName'        => 'banana',
                'itemId'          => 9876,
                'groupName'       => 'food',
                'groupId'         => 7654,
            ),
        ),
        array(
            array(
                'id'              => '',
                'dateTransaction' => '2999-01-01 99:99:99',
                'dateCreated'     => '2999-01-01 99:99:99',
                'amount'          => 12345,
                'currency'        => 'EUR',
                'currencyName'    => 'Euro',
                'currencySymbol'  => '€',
                'email'           => 'email@email.com',
                'role'            => 'user',
                'userId'          => 123,
                'locale'          => 'lv_LV',
                'timezone'        => 'Europe/Riga',
                'userName'        => 'Jane Doe',
                'itemName'        => 'banana',
                'itemId'          => 9876,
                'groupName'       => 'food',
                'groupId'         => 7654,
                'date'            => '2029-12-25',
                'created'         => '2029-12-25 23:59:10',
                'price'           => 123.45,
                'money'           => '€ 123,45',
            ),
        ),
    ),
);
