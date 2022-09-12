<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {
        $data['id_info'] = Auth::get_user_id();
        $loginUser = Auth::get_screen_name();
        $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

        if(count($channel_key) != 0) {
            $channel = DB::select()->from('channel')
            ->where_open()
            ->where('open', 0)->and_where('deleted_at', '0')
            ->where_close()
            ->or_where_open()
            ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
            ->or_where_close()
            ->execute()->as_array();
        } else {
            $channel = DB::select()->from('channel')
            ->where('open', 0)->and_where('deleted_at', '0')
            ->execute()->as_array();
        };
        
        $private = DB::select()->from('channel')->where('channelname', $loginUser)->execute()->as_array();
        
        $all_channels = array_merge($channel, $private);
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = $loginUser;
        $data['channelkey'] = $channel_key;
        $data['notification'] = DB::select()->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '0')
        ->execute()->as_array();
        // $data['loginUser'] = $loginUser;

        $read_check = [];
        $check = [];
        $test = [];

        $channel_name = array_column($all_channels, "channelname");

        foreach ($channel_name as $ch){
            $channelid = DB::select('each_channel_id')
            ->from('message')
            ->order_by('each_channel_id', 'desc')
            ->where('channelname', $ch)
            ->execute()->current();

            $msg_read_check = DB::select('channelname', 'read_id')
            ->from('message_read_check')
            ->where('channelname', $ch)
            ->and_where('username', $loginUser)
            ->execute()->current();

            if($channelid){
                $aaa = $channelid['each_channel_id'];
            }else{
                $aaa = '0';
            };

            if($msg_read_check){
                $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => $msg_read_check['read_id']];
            }else{
                $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => '0'];
            };
        };

        foreach ($read_check as $rc){
            $value = intval($rc -> id) - intval($rc -> read_id);
            $check[] = ['read_id' => $value];
        };

        foreach ($all_channels as $index => $aaa){
            
            $test[] = array_merge($aaa, $check[$index]);
        }
        

        $data['test'] = $test;

        // $data['data'] = array_merge($channel, $private);

        return View::forge('channel/channel', $data);

    }



}
