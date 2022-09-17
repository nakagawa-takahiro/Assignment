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
        $each_channel_id = Input::post('each_channel_id');

        $data = Model_Message::insert_message($username, $message, $channelname, $each_channel_id);

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
    
    public function post_bookmark_create()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $message_id = Input::post('id');
        $channelname = Input::post('channelname');
        $username = Auth::get_screen_name();
        $bookmark_state = Input::post('bookmark_state');

        $bookmark = Model_Message::register_bookmark($message_id, $username, $bookmark_state);
		$msgdata = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();
        
        $data = ['bookmark' => $bookmark, 'message_data' => $msgdata];

        return $this->response($data);

    }

       
    public function post_bookmark_delete()
    {
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;
        $username = Auth::get_screen_name();
        $channelname = Input::post('channelname');
        $bookmark_id = Input::post('id');
        $bookmark_state = Input::post('bookmark_state');
                
        $bookmark = Model_Message::delete_bookmark($username, $bookmark_id, $bookmark_state);
		$msgdata = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();

        $data = ['bookmark' => $bookmark, 'message_data' => $msgdata];

        return $this->response($data);

    }

    public function post_chat_comment()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $chat_id = Input::post('chat_id');

        $data = Model_Message::chat_comment($chat_id);
        
        return $this->response($data);

    }
   
    public function post_comment_post()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $chat_id = Input::post('chat_id');
        $channelname = Input::post('channelname');
        $commented_by = Input::post('commented_by');
        $comment_content = Input::post('comment_content');
        $mention_to = Input::post('mention_to');

        $data = Model_Message::comment_post($chat_id, $channelname, $commented_by, $comment_content, $mention_to);
        
        return $this->response($data);

    }

    public function post_read_message()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $username = Input::post('username');
        $channelname = Input::post('channelname');
        $read_id = Input::post('read_id');
        
        $data = Model_Message::read_message($username, $channelname, $read_id);
        
        return $this->response($data);

    }

    public function post_check_data()
    {

        $channelname = Input::post('channelname');

        $data = DB::select()->from('message')->where('channelname', $channelname)->execute()->as_array();
        
        return $this->response($data);

    }
    
}

// ===============================================
