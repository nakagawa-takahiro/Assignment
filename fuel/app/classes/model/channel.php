<?php

class Model_Channel extends \Model {

    public static function get_channels($loginUser)
    {

        $channel_key = DB::select('channel_id')
          ->from('channel_secret_key')
          ->where('username', $loginUser)
          ->execute()
          ->as_array();

        if(count($channel_key) !== 0) {
            $get_channel_data = DB::select()
            ->from('channel')
            ->where_open()
            ->where('open', 0)
            ->and_where('deleted_at', '0')
            ->where_close()
            ->or_where_open()
            ->where('id', 'in', $channel_key)
            ->and_where('deleted_at', '0')
            ->and_where('open', '1')
            ->or_where_close()
            ->execute()
            ->as_array();
        } else {
            $get_channel_data = DB::select()
            ->from('channel')
            ->where('open', 0)
            ->and_where('deleted_at', '0')
            ->execute()
            ->as_array();
        };
        
        $private_channel = DB::select()
          ->from('channel')
          ->where('channelname', $loginUser)
          ->execute()
          ->as_array();
        
        $all_channels_data = array_merge($get_channel_data, $private_channel);
        
        $unread = [];
        $channel_data = [];

        $channels_name = array_column($all_channels_data, "channelname");

        foreach ($channels_name as $channel_name){
            $current_id = DB::select('each_channel_id')
              ->from('message')
              ->order_by('each_channel_id', 'desc')
              ->where('channelname', $channel_name)
              ->execute()
              ->current();

            $each_id = DB::select('channelname', 'read_id')
              ->from('message_read_check')
              ->where('channelname', $channel_name)
              ->and_where('username', $loginUser)
              ->execute()
              ->current();

            if($current_id){
                $current_id = $current_id['each_channel_id'];
            }else{
                $current_id = '0';
            };

            if($each_id){
                $each_id = $each_id['read_id'];
            }else{
                $each_id = '0';
            };

            $unread_count = intval($current_id) - intval($each_id);

            $unread[] = ['unread_count' => "$unread_count"];

        };

        foreach ($all_channels_data as $index => $all_channel_data){
            $unread_value = $unread[$index];
            $channel_data[] = array_merge($all_channel_data, $unread_value);
        };

        return $channel_data;
    }

    public static function register_channel($loginUser, $channelname, $owner, $open){
        $insert = DB::insert('channel')
          ->set([
			'channelname' => "$channelname",
			'open' => $open,
			'owner' => "$owner"
		  ])
          ->execute();

        if($insert) {
            DB::insert('channel_secret_key')
              ->set([
                'channel_id' => $insert[0],
                'username' => $owner,
              ])
              ->execute();
        }

        $channel_key = DB::select('channel_id')
          ->from('channel_secret_key')
          ->where('username', $loginUser)
          ->execute()
          ->as_array();

        if(count($channel_key) !== 0) {
            $get_channel_data = DB::select()
              ->from('channel')
              ->where_open()
              ->where('open', 0)
              ->and_where('deleted_at', '0')
              ->where_close()
              ->or_where_open()
              ->where('id', 'in', $channel_key)
              ->and_where('deleted_at', '0')
              ->and_where('open', '1')
              ->or_where_close()
              ->execute()
              ->as_array();
        } else {
            $get_channel_data = DB::select()
            ->from('channel')
            ->where('open', 0)
            ->and_where('deleted_at', '0')
            ->execute()
            ->as_array();
        };
        
        $private_channel = DB::select()
          ->from('channel')
          ->where('channelname', $loginUser)
          ->execute()
          ->as_array();
        
        $all_channels_data = array_merge($get_channel_data, $private_channel);
        
        $unread = [];
        $channel_data = [];

        $channels_name = array_column($all_channels_data, "channelname");

        foreach ($channels_name as $channel_name){
            $current_id = DB::select('each_channel_id')
              ->from('message')
              ->order_by('each_channel_id', 'desc')
              ->where('channelname', $channel_name)
              ->execute()
              ->current();

            $each_id = DB::select('channelname', 'read_id')
              ->from('message_read_check')
              ->where('channelname', $channel_name)
              ->and_where('username', $loginUser)
              ->execute()
              ->current();

            if($current_id){
                $current_id = $current_id['each_channel_id'];
            }else{
                $current_id = '0';
            };

            if($each_id){
                $each_id = $each_id['read_id'];
            }else{
                $each_id = '0';
            };

            $unread_count = intval($current_id) - intval($each_id);

            $unread[] = ['unread_count' => "$unread_count"];

        };

        foreach ($all_channels_data as $index => $all_channel_data){
            $unread_value = $unread[$index];
            $channel_data[] = array_merge($all_channel_data, $unread_value);
        };

        return $channel_data;
    }

    public static function edit_channelvisibility($open, $id){
        DB::update('channel')
          ->value("open", $open)
          ->where('id', $id)
          ->execute();
        
        $loginUser = Auth::get_screen_name();

        $data = Model_Channel::get_channels($loginUser);

        return $data;
    }

    public static function edit_channelname($channelname, $newchannelname){
        $channel_update_result = DB::select('id')
          ->from('channel')
          ->where('channelname', $channelname)
          ->execute()
          ->as_array();
        $message_update_result = DB::select('id')
          ->from('message')
          ->where('channelname', $channelname)
          ->execute()
          ->as_array();
        $comment_update_result = DB::select('id')
          ->from('comment')
          ->where('channelname', $channelname)
          ->execute()
          ->as_array();

        if(count($channel_update_result) !== "0") {
            DB::update('channel')
              ->value("channelname", $newchannelname)
              ->where('id', 'in', $channel_update_result)
              ->execute();
        }

        if(count($message_update_result) !== "0") {
            DB::update('message')
              ->value("channelname", $newchannelname)
              ->where('id', 'in', $message_update_result)
              ->execute();
        }    
        
        if(count($comment_update_result) !== "0") {
            DB::update('comment')
              ->value("channelname", $newchannelname)
              ->where('id', 'in', $comment_update_result)
              ->execute();
        }

        $loginUser = Auth::get_screen_name();

        $newchanneldata = Model_Channel::get_channels($loginUser);

        $data = ['newchannelname' => $newchannelname, 'newchanneldata' => $newchanneldata];


        return $data;
    }

    public static function DM_create($channelname, $profile_user, $login_user){
        
        $result = DB::select('id')
          ->from('channel')
          ->where('channelname', $profile_user.'-'.$login_user)
          ->or_where('channelname', $login_user.'-'.$profile_user)
          ->execute()
          ->current();
        
        
        if($result){
            $channel = DB::select('channelname')
              ->from('channel')
              ->where('id', $result['id'])
              ->execute()
              ->current();

            $data = ['message_data' => [], 'channelname' => $channel];
            

        }else{
            $insert = DB::insert('channel')
              ->set([
                'channelname' => "$channelname",
                'open' => '1',
                'owner' => "dm"
              ])
              ->execute();
    
            DB::insert('channel_secret_key')
              ->set([
                'channel_id' => $insert[0],
                'username' => $profile_user
              ])
              ->execute();
    
            DB::insert('channel_secret_key')
              ->set([
                'channel_id' => $insert[0],
                'username' => $login_user
              ])
              ->execute();
    
            $channel = DB::select('channelname')
              ->from('channel')
              ->where('id', $insert[0])
              ->execute()
              ->current();

            $msg = DB::select()
              ->from('message')
              ->where('channelname', $channel)
              ->and_where('deleted_at', '0')
              ->execute()
              ->as_array();

            $data = ['message_data' => $msg, 'channelname' => $channel];
    
        }
        return $data;
    }
}