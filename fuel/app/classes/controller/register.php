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

        $result1 = DB::select('id')->from('channel')->where('channelname', $channelname)->execute()->as_array();
        $result2 = DB::select('id')->from('message')->where('channelname', $channelname)->execute()->as_array();
        $result3 = DB::select('id')->from('comment')->where('channelname', $channelname)->execute()->as_array();

        if(count($result1) != "0") {
            DB::update('channel')->value("channelname", $newchannelname)->where('id', 'in', $result1)->execute();
        }

        if(count($result2) != "0") {
            DB::update('message')->value("channelname", $newchannelname)->where('id', 'in', $result2)->execute();
        }    
        
        if(count($result3) != "0") {
            DB::update('comment')->value("channelname", $newchannelname)->where('id', 'in', $result3)->execute();
        }

        return $this->response($newchannelname);


        // Response::redirect('message/index'.$newchannelname);

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