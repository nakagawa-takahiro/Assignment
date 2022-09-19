<?php

class Controller_Auth extends Controller
{
    
    public function action_index()
    {
        
        if (Input::post()) {

            $username = Input::post('username');
            $password = Input::post('password');
            $email = Input::post('mail');
            Auth::create_user($username, $password, $email, 1);
            
            DB::insert('channel')->set([
                'channelname' => "$username",
                'owner' => "$username",
                'open' => 1,
                'private' => 1
            ])->execute();

            DB::insert('profile')->set([
                'username' => "$username",
                'self_introduction' => "よろしくお願いします。",
                'url_link' => "",
            ])->execute();

            return View::forge('auth/index');
        }
        return View::forge('auth/index');

    }   

    public function action_signup()
    {
        return View::forge('auth/sign_up');

    }

    public function action_login()
    {

        if (Input::post()) {
            
            $username = Input::post('username');
            $password = Input::post('password');
            // $data['username'] = $username;

            if (Auth::login($username, $password)) {

                Response::redirect('main/index');

            } else {
                echo "ログインに失敗しました";
                Response::redirect('auth/index');

            }
        }
        return View::forge('auth/index');
    }

    public function action_logout()
    {
        $result = Auth::logout();
        Response::redirect('auth/index');



    }

}