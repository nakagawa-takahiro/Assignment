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


        $channelname = Input::post('channelname');
		$msgdata = DB::select()->from('message')->where('channelname', $channelname)->and_where('deleted_at', '0')->execute()->as_array();

        $current_message = DB::select('each_channel_id')->from('message')
        ->order_by('each_channel_id', 'desc')
        ->where('channelname', $channelname)->and_where('deleted_at', '0')
        ->execute()->current();
        if($current_message){
            $current_message = $current_message['each_channel_id'];
        }else{
            $current_message = "0";
        }

		$channelData = DB::select()->from('channel')->where('channelname', $channelname)->execute()->current();

        $data = ['data' => $msgdata, 'current_message' => $current_message, 'channelData' => $channelData];

        return $this->response($data);
    }
}