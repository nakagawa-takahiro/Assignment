<?php

class Model_Invite extends \Model {

    /**
     * 招待したことの記録
     * @param $channelname string           招待が発生したチャンネルの名前
     * @param $invited_user string          招待されたユーザーの名前
     * @param $username_from string         招待したユーザーの名前
     */
    public static function insert_invitation($channelname, $invited_user, $username_from)
    {

        $insert = DB::insert('invite')
          ->set([
            'username_to' => $invited_user,
            'username_from' => $username_from,
            'channelname' => $channelname['channelname']
          ])
          ->execute();
        
    }

    /**
     * 招待を確認したことの記録
     * @param $channelname string           招待が発生したチャンネルの名前
     * @param $username_to string           招待されたユーザーの名前
     * @param $username_from string         招待したユーザーの名前
     * @return array                        残った招待通知のデータ
     */
    public static function delete_invitation($channelname, $username_to, $username_from)
    {

        $date = date('Y-m-d H:i:s');
        $insert = DB::update('invite')
          ->value('checked_at', $date)
          ->where('username_to', $username_to)
          ->and_where('channelname', $channelname)
          ->and_where('username_from', $username_from)
          ->execute();

        $invitations = DB::select('username_to', 'username_from', 'channelname')
          ->from('invite')
          ->where('username_to', $username_to)
          ->and_where('checked_at', 'is', null)
          ->execute()
          ->as_array();

        return $invitations;
    }
}