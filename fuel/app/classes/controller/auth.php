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
                'private' => 1
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

                View::set_global('usr', $username);
                // return View::forge('message/index');
                Response::redirect('channel/index');
                // return $view;

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