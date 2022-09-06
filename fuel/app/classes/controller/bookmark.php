<?php

class Controller_Bookmark extends Controller
{
    
    public function action_index()
    {
        $user = Arr::get(Auth::get_user_id(),1);
        $loginUser = Auth::get_screen_name();
        $bookmark_id = DB::select('message_id')->from('bookmark')->where('username', $loginUser)->execute()->as_array();
		
        $data['data'] = DB::select()
        ->from('message')
        ->where('id', 'in', $bookmark_id)
        ->and_where('deleted_at', '0')
        ->execute()
        ->as_array();
        return View::forge('message/bookmark', $data);

        // print_r($user);
    }   


}