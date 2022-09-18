<?php

class Controller_Main extends Controller
{
    
    public function action_index()
    {
        $loginUser = Auth::get_screen_name();
        
        $data['data'] = Model_Channel::get_channels($loginUser);
		$data['profdata'] = DB::select()->from('profile')->where('username', $loginUser)->and_where('deleted_at', '0')->execute()->current();

        $data['loginUser'] = $loginUser;

        $mentions = DB::select('id', 'channelname', 'chat_id', 'commented_by', 'mention_to', 'comment_content', 'posted_at')
        ->from('comment')
        ->where('mention_to', $loginUser)->and_where('read_check', '=', NULL)
        ->execute()->as_array();
        $invitations = DB::select('username_to', 'username_from', 'channelname')->from('invite')
        ->where('username_to', $loginUser)->and_where('checked_at', '=', NULL)
        ->execute()->as_array();

        // $notifications=[];

        // foreach($mentions as $mention){
        //     $newmentions[] = [

        //     ]
        // }

        $data['notification'] = $invitations;
        $data['mention'] = $mentions;

        $bookmark_id = DB::select('message_id')->from('bookmark')->where('username', $loginUser)->and_where('deleted_at', '0')->execute()->as_array();
		
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
		$data['comment'] = DB::select('id', 'channelname', 'chat_id', 'commented_by', 'mention_to', 'comment_content', 'posted_at')->from('comment')->where('deleted_at', '0')->execute()->as_array();
        $data['users'] = DB::select('username')->from('users')->distinct()->execute()->as_array();

        return View::forge('main', $data);
        

    }

}
