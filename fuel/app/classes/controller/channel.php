<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
        $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

        if(count($channel_key) != 0) {
            $data = DB::select()->from('channel')
            ->where_open()
            ->where('open', 0)->and_where('deleted_at', '0')
            ->where_close()
            ->or_where_open()
            ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
            ->or_where_close()
            ->execute()->as_array();
        } else {
            $data = DB::select()->from('channel')
            ->where('open', 0)->and_where('deleted_at', '0')
            ->execute()->as_array();
        }


        
        $private = DB::select()->from('channel')->where('channelname', $loginUser)->execute()->as_array();
        
        $data['data'] = array_merge($data, $private);
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = $loginUser;
        $data['channelkey'] = $channel_key;
        $data['notification'] = DB::select()->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '0')
        ->execute()->as_array();
        // $data['loginUser'] = $loginUser;


        return View::forge('channel/channel', $data);

        // print_r($user);
    }



}
