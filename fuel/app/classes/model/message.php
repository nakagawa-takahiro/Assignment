<?php

class Model_Message extends \Model {

    public static function insert_message($username, $message, $channelname, $each_channel_id)
    {
        $insert = DB::insert('message')->set([
            'username' => $username,
            'content' => $message,
            'channelname' => $channelname,
            'each_channel_id' => $each_channel_id
        ])->execute();

        DB::update('message_read_check')->value('read_id', $each_channel_id)
        ->where('username', $username)->and_where('channelname', $channelname)->execute();

        $data = DB::select()->from('message')->where('id', $insert[0])->execute()->current();
        
        return $data;
    }

    public static function delete_message($id, $deleted_at)
    {
        DB::update('message')->value("deleted_at", $deleted_at)->where('id', $id)->execute();

        $data = DB::select()->from('message')->where('id', $id)->execute()->current();
        
        return $data;
    }

    public static function edit_message($id, $content)
    {
        DB::update('message')->value("content", $content)->where('id', $id)->execute();

        $data = DB::select()->from('message')->where('id', $id)->execute()->current();
        
        return $data;
    }

    public static function click_like($id, $res_good)
    {
        DB::update('message')->value("res_good", $res_good)->where('id', $id)->execute();

        $data = DB::select()->from('message')->where('id', $id)->execute()->current();
        
        return $data;
    }

    public static function click_dislike($id, $res_bad)
    {
        DB::update('message')->value("res_bad", $res_bad)->where('id', $id)->execute();

        $data = DB::select()->from('message')->where('id', $id)->execute()->current();
        
        return $data;
    }
}