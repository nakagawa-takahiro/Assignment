<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
        $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

        if(count($channel_key) != 0) {
            $get_channel_data = DB::select()->from('channel')
            ->where_open()
            ->where('open', 0)->and_where('deleted_at', '0')
            ->where_close()
            ->or_where_open()
            ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
            ->or_where_close()
            ->execute()->as_array();
        } else {
            $get_channel_data = DB::select()->from('channel')
            ->where('open', 0)->and_where('deleted_at', '0')
            ->execute()->as_array();
        };
        
        $private_channel = DB::select()->from('channel')->where('channelname', $loginUser)->execute()->as_array();
        
        $all_channels_data = array_merge($get_channel_data, $private_channel);
        
        $unread = [];
        $channel_data = [];

        $channels_name = array_column($all_channels_data, "channelname");

        foreach ($channels_name as $channel_name){
            $current_id = DB::select('each_channel_id')
            ->from('message')
            ->order_by('each_channel_id', 'desc')
            ->where('channelname', $channel_name)
            ->execute()->current();

            $each_id = DB::select('channelname', 'read_id')
            ->from('message_read_check')
            ->where('channelname', $channel_name)
            ->and_where('username', $loginUser)
            ->execute()->current();

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
        

        $data['data'] = $channel_data;
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = $loginUser;
        $data['channelkey'] = $channel_key;
        $data['notification'] = DB::select()->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '0')
        ->execute()->as_array();

        // $data['data'] = array_merge($channel, $private);

        return View::forge('channel/channel', $data);

    }



}
