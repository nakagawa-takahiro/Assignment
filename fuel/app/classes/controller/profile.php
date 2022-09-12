<?php

class Controller_Profile extends Controller
{
    
    public function action_index($username)
    {

        $loginUser = Auth::get_screen_name();        
		$data['data'] = DB::select()->from('profile')->where('username', $username)->and_where('deleted_at', '0')->execute()->current();
        $data['loginUser'] = $loginUser;        
        $data['profileUser'] = $username;        
        return View::forge('profile/profile', $data);

    }   



}
