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
        
        $insert = DB::insert('channel')->set([
			'channelname' => "$channelname",
			'open' => $open,
			'owner' => "$owner"
		])->execute();

        DB::insert('channel_secret_key')->set([
			'channel_id' => $insert[0],
			'username' => $owner,
		])->execute();


        $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

        if(count($channel_key) != 0) {
            $channel = DB::select()->from('channel')
            ->where_open()
            ->where('open', 0)->and_where('deleted_at', '0')
            ->where_close()
            ->or_where_open()
            ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
            ->or_where_close()
            ->execute()->as_array();
        } else {
            $channel = DB::select()->from('channel')
            ->where('open', 0)->and_where('deleted_at', '0')
            ->execute()->as_array();
        };

		// $public = DB::select()->from('channel')->where('open', "0")->and_where('deleted_at', '0')->execute()->as_array();
        $private = DB::select()->from('channel')->where('channelname', $owner)->execute()->as_array();
        
        $all_channels = array_merge($channel, $private);


        $read_check = [];
        $check = [];
        $data = [];

        $channel_name = array_column($all_channels, "channelname");

        foreach ($channel_name as $ch){
            $channelid = DB::select('each_channel_id')
            ->from('message')
            ->order_by('each_channel_id', 'desc')
            ->where('channelname', $ch)
            ->execute()->current();

            $msg_read_check = DB::select('channelname', 'read_id')
            ->from('message_read_check')
            ->where('channelname', $ch)
            ->and_where('username', $loginUser)
            ->execute()->current();

            if($channelid){
                $aaa = $channelid['each_channel_id'];
            }else{
                $aaa = '0';
            };

            if($msg_read_check){
                $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => $msg_read_check['read_id']];
            }else{
                $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => '0'];
            };
        };


        foreach ($read_check as $rc){
            $value = intval($rc -> id) - intval($rc -> read_id);
            $check[] = ['read_id' => $value];
        };

        foreach ($all_channels as $index => $aaa){
            
            $data[] = array_merge($aaa, $check[$index]);
        }
        
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

        $result = DB::select('id')->from('channel')
          ->where('channelname', $username1.'-'.$username2)
          ->or_where('channelname', $username2.'-'.$username1)
          ->execute()->current();
        
        
        if($result){
            $data = DB::select('channelname')->from('channel')
              ->where('id', $result['id'])
              ->execute()->current();

        }else{
            $insert = DB::insert('channel')->set([
                'channelname' => "$channelname",
                'open' => '1',
                'owner' => "dm"
            ])->execute();
    
            DB::insert('channel_secret_key')->set([
                'channel_id' => $insert[0],
                'username' => $username1
    
            ])->execute();
    
            DB::insert('channel_secret_key')->set([
                'channel_id' => $insert[0],
                'username' => $username2
    
            ])->execute();
    
            $data = DB::select('channelname')->from('channel')
              ->where('id', $insert[0])
              ->execute()->current();
    
        }
               
        return $this->response($data);

    }   

}