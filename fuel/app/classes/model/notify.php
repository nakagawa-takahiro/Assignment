<?php

class Model_Notify extends \Model {

    public static function insert_notification($username_to, $username_from, $type)
    {

        $insert = DB::insert('notification')->set([

            'username_to' => $username_to,
            'username_from' => $username_from,
            'type' => $type

        ])->execute();
        
    }
}