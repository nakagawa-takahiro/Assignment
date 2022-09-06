<?php

class Controller_Message extends Controller
{
    
    public function action_index($channelname)
    {

		$data['data'] = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = Auth::get_screen_name();
        $data['channelname'] = $channelname;
        // $data['loginUser'] = $loginUser;
        return View::forge('message/index', $data);

    }   


}
