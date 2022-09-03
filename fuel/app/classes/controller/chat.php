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
        $channelname = Input::post('channelname');

        $insert = DB::insert('message')->set([
			'username' => "$username",
			'content' => "$message",
            'channelname' => "$channelname"
		])->execute();

        $data = DB::select()->from('message')->where('id', $insert[0])->execute()->current();

        $id = $data['id'];
        $username = $data['username'];
        $content = $data['content'];
        $posted_at = $data['posted_at'];

        $res = array(
            'id' => $id,
            'username' => $username,
            'content' => $message,
            'posted_at' => $posted_at
        );

        return $this->response($res);
        // return $this->response($data, 200);
    }

    public function post_chat_delete()
    {
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );
        return $this->response($res);

        endif;

        $id = Input::post('id');
        $channelname = Input::post('channelname');
        $result = DB::delete('message')
        ->where('id', $id)->execute();
        $data = DB::select()->from('message')
        ->where('channelname', $channelname)
    	->and_where('id', $id)
        ->execute();
        return $this->response($data);
    }

    public function post_chat_edit()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $id = Input::post('id');
        $message = Input::post('content');
        $channelname = Input::post('channelname');
        $result = DB::update('message')->value("content", $message)->where('id', $id)->execute();

        $data = DB::select()->from('message')->where('channelname', $channelname)->execute();
        return $this->response($data);

    }

    public function post_post_good()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $id = Input::post('id');
        $res_good = Input::post('res_good');
        $result = DB::update('message')->value("res_good", $res_good)->where('id', $id)->execute();
        $channelname = Input::post('channelname');
        $data = DB::select()->from('message')->where('channelname', $channelname)->execute();
        return $this->response($data);

    }

    public function post_post_bad()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $id = Input::post('id');
        $res_bad = Input::post('res_bad');
        $result = DB::update('message')->value("res_bad", $res_bad)->where('id', $id)->execute();
        $channelname = Input::post('channelname');
        $data = DB::select()->from('message')->where('channelname', $channelname)->execute();
        return $this->response($data);

    }
    
}

// ===============================================
