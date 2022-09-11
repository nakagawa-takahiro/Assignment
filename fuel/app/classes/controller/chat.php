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
        $username = Auth::get_screen_name();
        $bookmark_state = Input::post('bookmark_state');

        $result =  DB::select()->from('bookmark')->where('username', $username)->and_where('message_id', $message_id)->execute()->as_array();

        if(!$result) {
            $insert = DB::insert('bookmark')->set([
                'username' => $username,
                'message_id' => $message_id,
            ])->execute();
        }else{

            DB::update('bookmark')->value("deleted_at", $bookmark_state)->where('id', $bookmark_id)->execute();
        }

        $data = DB::select('message_id')->from('bookmark')->where('username', $username)->and_where('id', $insert[0])->execute()->as_array();
        
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

        $bookmark_id = Input::post('id');
        $bookmark_state = Input::post('bookmark_state');
        
        DB::update('bookmark')->value("deleted_at", $bookmark_state)->where('message_id', $bookmark_id)->execute();

        $data = DB::select()->from('bookmark')->where('username', $username)->and_where('deleted_at', "0")->execute()->as_array();
        
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
        // $username = Auth::get_screen_name();
        // $insert = DB::insert('bookmark')->set([
        //     'username' => $username,
        //     'message_id' => $message_id,
        // ])->execute();

        $data = DB::select()->from('comment')->where('chat_id', $chat_id)->execute()->as_array();
        
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
        $insert = DB::insert('comment')->set([
            'chat_id' => $chat_id,
            'channelname' => $channelname,
            'mention_to' => $mention_to,
            'commented_by' => $commented_by,
            'comment_content' => $comment_content,
        ])->execute();

        $data = DB::select()->from('comment')->where('chat_id', $chat_id)->execute()->as_array();
        
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

        $result = DB::select()->from('message_read_check')
        ->where('username', $username)->and_where('channelname', $channelname)
        ->execute()->as_array();

        if($result){
            DB::update('message_read_check')->value('read_id', $read_id)
            ->where('username', $username)->and_where('channelname', $channelname)->execute();    
        }else{

        $insert = DB::insert('message_read_check')->set([
            'username' => $username,
            'channelname' => $channelname,
            'read_id' => $read_id
        ])->execute();
        }

        $data = DB::select()->from('message_read_check')->execute()->as_array();
        
        return $this->response($data);

    }
    
}

// ===============================================
