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

        $data = DB::select()->from('messages')->order_by('id','desc')->limit(1)->execute();

        foreach($data as $datum)
        {
            $id = $datum['id'];
            $username = $datum['username'];
            $content = $datum['content'];
            $posted_at = $datum['posted_at'];
        }

        return $this->response(array(
            'id' => $id,
            'username' => $username,
            'content' => $message,
            'posted_at' => $posted_at)
        );
        // return $this->response($data, 200);
    }
    
}

// ===============================================
