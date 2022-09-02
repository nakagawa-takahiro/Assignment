<?php

// ===============================================

class Controller_Chat extends Controller_Rest
{
    
    protected $default_format = 'json';

    public function post_chat_post()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $username = Input::post('username');
        $message = Input::post('content');

        $insert = DB::insert('messages')->set([
			'username' => "$username",
			'content' => "$message",
		])->execute();

        $data = DB::select()->from('messages')->where('id', $insert[0])->execute()->current();

        $id = $data['id'];
        $username = $data['username'];
        $content = $data['content'];
        $posted_at = $data['posted_at'];

        $res = array(
            'success' => true,
            'id' => $id,
            'username' => $username,
            'content' => $message,
            'posted_at' => $posted_at
        );

        return $this->response($res);
        // return $this->response($data, 200);
    }
    
}

// ===============================================
