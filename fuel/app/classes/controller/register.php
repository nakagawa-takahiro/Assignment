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

        $channelname = Input::post('channelname');
        $owner = Input::post('owner');
        $open = Input::post('open');
        
        $insert = DB::insert('channel')->set([
			'channelname' => "$channelname",
			'open' => $open,
			'owner' => "$owner"
		])->execute();

        DB::insert('channel_secret_key')->set([
			'channelid' => $insert[0],
			'username' => $owner,
		])->execute();

		$data = DB::select()->from('channel')->where('open', "0")->and_where('deleted_at', '0')->execute()->as_array();
        $private = DB::select()->from('channel')->where('channelname', $owner)->execute()->as_array();
        
        $data = array_merge($data, $private);
        
        return $this->response($data);
    }

    public function post_edit()
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

        DB::update('channel')->value("open", $open)->where('id', $id)->execute();

        $data = DB::select()->from('channel')->where('id', $id)->execute()->current();

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

		// $data = DB::select()->from('channel_secret_key')->execute()->as_array();

        // return $this->response($data);

    }

}