<?php

class Controller_Channel extends Controller
{
    
    public function action_index()
    {

		$data['data'] = DB::select()->from('channel')->execute()->as_array();
        $data['user'] = Arr::get(Auth::get_user_id(),1);
        $data['loginUser'] = Auth::get_screen_name();
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
