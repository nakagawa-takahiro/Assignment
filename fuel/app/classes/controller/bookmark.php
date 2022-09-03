<?php

class Controller_Bookmark extends Controller
{
    
    public function action_index()
    {
        $user = Arr::get(Auth::get_user_id(),1);
        $loginUser = Auth::get_screen_name();
		$data['data'] = DB::select()
        ->from('message')
        ->where('username', $loginUser)
        ->and_where('bookmark', 1)
        ->execute()
        ->as_array();
        return View::forge('message/bookmark', $data);

        // print_r($user);
    }   


}
