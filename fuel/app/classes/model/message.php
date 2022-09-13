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

        $result = DB::select()->from('message_read_check')->where('username', $username)->and_where('channelname', $channelname)->execute()->as_array();
        if($result){
            DB::update('message_read_check')->value('read_id', $each_channel_id)
              ->where('username', $username)->and_where('channelname', $channelname)->execute();
        }else{
            DB::insert('message_read_check')->set([
                'username' => $username,
                'channelname' => $channelname,
                'read_id' => $each_channel_id
            ])->execute();
        }

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

    public static function register_bookmark($message_id, $username, $bookmark_state)
    {
        $result =  DB::select()->from('bookmark')->where('username', $username)->and_where('message_id', $message_id)->execute()->as_array();

        if(!$result) {
            $insert = DB::insert('bookmark')->set([
                'username' => $username,
                'message_id' => $message_id,
            ])->execute();
            
            $data = DB::select('message_id')->from('bookmark')->where('username', $username)->and_where('id', $insert[0])->execute()->as_array();

        }else{

            DB::update('bookmark')->value("deleted_at", $bookmark_state)->where('username', $username)->and_where('message_id', $message_id)->execute();
            $data = DB::select('message_id')->from('bookmark')->where('username', $username)->and_where('message_id', $message_id)->execute()->as_array();
        
        }

        
        return $data;
    }

    public static function delete_bookmark($username, $bookmark_id, $bookmark_state)
    {
        DB::update('bookmark')->value("deleted_at", $bookmark_state)->where('username', $username)->and_where('message_id', $bookmark_id)->execute();
        
        $data = DB::select()->from('bookmark')->where('username', $username)->and_where('deleted_at', "0")->execute()->as_array();
        
        return $data;
    }

    public static function chat_comment($chat_id)
    {

        $data = DB::select()->from('comment')->where('chat_id', $chat_id)->execute()->as_array();        
        
        return $data;
    }

    public static function comment_post($chat_id, $channelname, $commented_by, $comment_content, $mention_to)
    {
        $insert = DB::insert('comment')->set([
            'chat_id' => $chat_id,
            'channelname' => $channelname,
            'mention_to' => $mention_to,
            'commented_by' => $commented_by,
            'comment_content' => $comment_content,
        ])->execute();

        $data = DB::select()->from('comment')->where('chat_id', $chat_id)->execute()->as_array();
        
        return $data;
    }

    public static function read_message($username, $channelname, $read_id)
    {
        $result = DB::select()->from('message_read_check')
        ->where('username', $username)->and_where('channelname', $channelname)
        ->execute()->as_array();

        if($result){
            DB::update('message_read_check')->value('read_id', $read_id)
            ->where('username', $username)->and_where('channelname', $channelname)->execute();    
        }else{

        $insert = DB::insert('message_read_check')->set([
            'username' => $username,
            'channelname' => $channelname,
            'read_id' => $read_id
        ])->execute();
        }
        $data = DB::select()->from('message_read_check')->execute()->as_array();

        return $data;
    }

}