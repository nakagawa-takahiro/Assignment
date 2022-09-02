<?php


// ===============================================

class Controller_Message extends Controller
{
    
    public function action_index($loginUser)
    {

		$data['data'] = DB::select()->from('messages')->execute()->as_array();
		$data['contents'] = DB::select('username','content')->from('messages')->execute()->as_array();
        $data['token_key'] = Config::get('security.csrf_token_key');
        $data['token'] = Security::fetch_token();

        $data['loginUser'] = $loginUser;
        return View::forge('message/index', $data);

        // print_r($user);
    }   


}

// ===============================================