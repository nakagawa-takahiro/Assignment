<?php

// ===============================================

class Controller_Chat extends Controller_Rest
{
    
    protected $default_format = 'json';

    public function post_chat_post()
    {
        $username = Input::post('username');
        $message = Input::post('content');
        DB::insert('messages')->set(array(
			'username' => "$username",
			'content' => "$message",
		))->execute();

        // $data = DB::select()->from('messages')->execute()->as_array();

        $this->response(array('username' => $username, 'content' => $message) 
        );
        // return $this->response($data, 200);
    }
    
}

// ===============================================
