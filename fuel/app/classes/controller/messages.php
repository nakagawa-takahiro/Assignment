<?php

class Controller_Messages extends Controller_Rest
{
    
    protected $default_format = 'json';

    public function post_get_message()
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
		$msgdata = DB::select()
          ->from('message')
          ->where('channelname', $channelname)
          ->and_where('deleted_at', '0')
          ->execute()
          ->as_array();

        $current_message = DB::select('each_channel_id')
          ->from('message')
          ->order_by('each_channel_id', 'desc')
          ->where('channelname', $channelname)
          ->and_where('deleted_at', '0')
          ->execute()
          ->current();
        
        if($current_message){
            $current_message = $current_message['each_channel_id'];
        }else{
            $current_message = "0";
        }

		$channelData = DB::select()
          ->from('channel')
          ->where('channelname', $channelname)
          ->execute()
          ->current();
		
        $users = DB::select('username')
          ->from('message')
          ->distinct(true)
          ->where('channelname', $channelname)
          ->execute()
          ->as_array();

        $newchanneldata = Model_Channel::get_channels($loginUser);

        $data = [
            'data' => $msgdata,
            'users' => $users,
            'current_message' => $current_message, 
            'channelData' => $channelData, 
            'current_message' => $current_message, 
            'channeldata' => $newchanneldata
        ];

        return $this->response($data);
    }

    public function post_get_profile()
    {
        $profileUser = Input::post('profile_user');
		$data = DB::select()
          ->from('profile')
          ->where('username', $profileUser)
          ->and_where('deleted_at', '0')
          ->execute()
          ->current();
        
        return $data;
    }
}