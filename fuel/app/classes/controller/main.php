<?php

class Controller_Main extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
        
        $data['data'] = Model_Channel::get_channels($loginUser);
        $data['loginUser'] = $loginUser;
        $data['notification'] = DB::select()->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '0')
        ->execute()->as_array();


        $bookmark_id = DB::select('message_id')->from('bookmark')->where('username', $loginUser)->execute()->as_array();
		
        if(count($bookmark_id) != 0) {
            $bookmark = DB::select()
            ->from('message')
            ->where('id', 'in', $bookmark_id)
            ->and_where('deleted_at', '0')
            ->execute()
            ->as_array();
        } else {
            $bookmark = [];
        };

        $data['bookdata'] = $bookmark;
		$data['comment'] = DB::select()->from('comment')->where('deleted_at', '0')->execute()->as_array();
        $data['users'] = DB::select('username')->from('users')->distinct()->execute()->as_array();

        return View::forge('main', $data);
        

    }

}
