<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
        
        $data['data'] = Model_Channel::get_channels($loginUser);
        $data['loginUser'] = $loginUser;
        $data['notification'] = DB::select()->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '0')
        ->execute()->as_array();

        return View::forge('channel/channel', $data);

    }

}
