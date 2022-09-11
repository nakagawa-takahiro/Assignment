<?php

class Controller_Message extends Controller
{
    
    public function action_index($channelname)
    {

		$data['data'] = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();
		$current_message = DB::select('each_channel_id')->from('message')
        ->order_by('each_channel_id', 'desc')
        ->where('channelname', $channelname)->and_where('deleted_at', '0')
        ->execute()->current();
        $data['current_message'] = $current_message['each_channel_id'];
        
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = Auth::get_screen_name();
        $loginUser = Auth::get_screen_name();
        $data['channelname'] = $channelname;
		$data['channelData'] = DB::select()->from('channel')->where('channelname', $channelname)->execute()->current();
		$data['comment'] = DB::select()->from('comment')->where('deleted_at', '0')->execute()->as_array();
        $data['users'] = DB::select('username')->from('users')->distinct()->execute()->as_array();
        $data['bookmark'] = DB::select()->from('bookmark')->where('username', $loginUser)->execute()->as_array();
        // $data['bookmark'] = DB::select()->from('bookmark')->where('deleted_at', '0')->and_where('username', $loginUser)->execute()->as_array();
        return View::forge('message/index', $data);

    }   


}
