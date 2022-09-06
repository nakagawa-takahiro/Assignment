<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
		$data = DB::select()->from('channel')->where('private', 0)->and_where('deleted_at', '0')->execute()->as_array();
        $private = DB::select()->from('channel')->where('channelname', $loginUser)->execute()->as_array();
        $data['data'] = array_merge($data, $private);
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = $loginUser;
        // $data['loginUser'] = $loginUser;
        return View::forge('channel/channel', $data);

        // print_r($user);
    }

    public function post_register()
    {
        $channel = Input::post('channel');
        $data['loginUser'] = Auth::get_screen_name();
        $insert = DB::insert('channel')->set([
			'channelname' => "$channel"
		])->execute();
        Response::redirect('channel/index');

    }


}
