<?php


// ===============================================

class Controller_Message extends Controller
{
    
    public function action_index($loginUser)
    {

		$data['data'] = DB::select()->from('messages')->execute()->as_array();
		$data['contents'] = DB::select('username','content')->from('messages')->execute()->as_array();

        $data['loginUser'] = $loginUser;
        return View::forge('message/index', $data);

        // print_r($user);
    }   


}

// ===============================================