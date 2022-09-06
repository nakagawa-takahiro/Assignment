<?php

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

        $data = Model_Message::insert_message($username, $message, $channelname);

        return $this->response($data);
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
        $deleted_at = date('Y-m-d H:i:s');
        
        $data = Model_Message::delete_message($id, $deleted_at);

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
        $content = Input::post('content');

        $data = Model_Message::edit_message($id, $content);

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
        
        $data = Model_Message::click_like($id, $res_good);

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
        
        $data = Model_Message::click_dislike($id, $res_bad);

        return $this->response($data);

    }
    
    public function post_bookmark()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $message_id = Input::post('message_id');
        $username = Auth::get_screen_name();
        $insert = DB::insert('bookmark')->set([
            'username' => $username,
            'message_id' => $message_id,
        ])->execute();

        // $data = DB::select(message_id)->from('bookmark')->where('username', $username)->execute();
        
        // return $this->response($data);

    }
    
}

// ===============================================
