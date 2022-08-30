<?php


// ===============================================

class Controller_Message extends Controller
{
    
    public function action_index()
    {

		$data['data'] = DB::select()->from('messages')->execute()->as_array();
		$data['contents'] = DB::select('content')->from('messages')->execute()->as_array();

        return View::forge('message/index', $data);

        // print_r($user);
    }   

    // public function action_post()
    // {
    //     $username = Input::post('usrname');
    //     $message = Input::post('content');
	// 	DB::insert('messages')->set(array(
	// 		'username' => "$username",
	// 		'content' => "$message",
	// 	))->execute();

    //     Response::redirect('message/index');

    // }

}

// ===============================================