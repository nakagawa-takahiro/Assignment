<?php

class Controller_Edit extends Controller_Rest
{
    protected $default_format = 'json';

    public function post_edit()
    {

        // トークンチェック    
        if (!\Security::check_token()) :
            $res = array(
            'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
        );

        return $this->response($res);

        endif;

        $username = Input::post('username');
        $content = Input::post('content');
        $url_link = Input::post('url_link');        
        
        
        $result = DB::update('profile')
          ->set([
            'self_introduction' => $content,
            'url_link' => $url_link
          ])
          ->where('username', $username)
          ->execute();
        
        $data = DB::select()
          ->from('profile')
          ->where('username', $username)
          ->and_where('deleted_at', '0')
          ->execute()->current();
        
        return $this->response($data);

    }   

}