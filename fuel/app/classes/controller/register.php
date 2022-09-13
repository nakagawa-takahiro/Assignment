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
        
        $insert = DB::insert('channel_secret_key')->set([
			'channel_id' => "$channel_id",
			'username' => $invited_user,
		])->execute();

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