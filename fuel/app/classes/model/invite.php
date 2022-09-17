<?php

class Model_Invite extends \Model {

    public static function insert_invitation($channelname, $invited_user, $username_from)
    {

        $insert = DB::insert('invite')->set([

            'username_to' => $invited_user,
            'username_from' => $username_from,
            'channelname' => $channelname['channelname']

        ])->execute();
        
    }

    public static function delete_invitation($channelname, $username_to, $username_from)
    {

        $date = date('Y-m-d H:i:s');
        $insert = DB::update('invite')
        ->value('checked_at', $date)
        ->where('username_to', $username_to)->and_where('channelname', $channelname)->and_where('username_from', $username_from)
        ->execute();

        $invitations = DB::select('username_to', 'username_from', 'channelname')->from('invite')
        ->where('username_to', $username_to)->and_where('checked_at', '=', NULL)
        ->execute()->as_array();

        return $invitations;
    }
}