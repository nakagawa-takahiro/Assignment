<?php

class Controller_Register extends Controller_Rest
{
    protected $default_format = 'json';

    public function post_register()
    {
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;
        $loginUser = Auth::get_screen_name();
        $channelname = Input::post('channelname');
        $owner = Input::post('owner');
        $open = Input::post('open');

        $data = Model_Channel::register_channel($loginUser, $channelname, $owner, $open);
        
        return $this->response($data);
    }

    public function post_edit_channelvisibility()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);
        endif;

        $open = Input::post('open');
        $id = Input::post('channel_id');

        $data = Model_Channel::edit_channelvisibility($open, $id);

        return $this->response($data);

    }

    public function post_edit_channelname()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);
        endif;

        $channelname = Input::post('channelname');
        $newchannelname = Input::post('newChannelname');

        $data = Model_Channel::edit_channelname($channelname, $newchannelname);        

        return $this->response($data);

    }


    public function post_invite()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);
        endif;

        $channel_id = Input::post('channel_id');
        $invited_user = Input::post('invited_user');
        $username = Input::post('username');
        $message = Input::post('content');
        $each_channel_id = Input::post('each_channel_id');

        $username_from = Auth::get_screen_name();
        $channelname = DB::select()->from('channel')
        ->where('id', $channel_id)
        ->execute()->current();
        
        Model_Invite::insert_invitation($channelname, $invited_user, $username_from);
        
        $insert = DB::insert('channel_secret_key')->set([
			'channel_id' => "$channel_id",
			'username' => $invited_user,
		])->execute();

        $channelname = $channelname['channelname'];
        
        $data = Model_Message::insert_message($username, $message, $channelname, $each_channel_id);
        
        return $this->response($data);

    }

    public function post_invite_checked()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);
        endif;

        $channelname = Input::post('channelname');
        $username_to = Input::post('username_to');
        $username_from = Input::post('username_from');
        
        $invite = Model_Invite::delete_invitation($channelname, $username_to, $username_from);
        $msgdata = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();

        $data = ['message_data' => $msgdata, 'invite' => $invite, 'channelname' => $channelname];

        return $this->response($data);

    }

    public function post_mention_checked()
    {
        
        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);
        endif;

        $chat_id = Input::post('chat_id');
        $message_id = Input::post('message_id');
        $commented_by = Input::post('username');
        $mention_to = Auth::get_screen_name();

        $mention = Model_Message::read_check($chat_id, $commented_by, $mention_to);
        $comment_data = Model_Message::chat_comment($message_id);
        $msgdata = DB::select()->from('message')->where('id', $message_id)->and_where('deleted_at', '0')->execute()->current();

        $data = ['comment_data' => $comment_data, 'mention' => $mention, 'message_data' => $msgdata];

        return $this->response($data);

    }

    public function post_DM_create()
    {

        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $channelname = Input::post('channelname');
        $username1 = Input::post('profile_user');
        $username2 = Input::post('login_user');

        $data = Model_Channel::DM_create($channelname, $username1, $username2);        

        return $this->response($data);

    }   

}