<?php

class Model_Message extends \Model {


    /**
     * メッセージをデータベースに登録
     * @param $username string           ユーザー名
     * @param $message string            メッセージ内容
     * @param $channelname string        メッセージが送信されたチャンネル名
     * @param $each_channel_id string    そのチャンネルで送信されたメッセージの最新のID
     * @return array                     登録したメッセージデータ
     */
    public static function insert_message($username, $message, $channelname, $each_channel_id)
    {
        $insert = DB::insert('message')
          ->set([
            'username' => $username,
            'content' => $message,
            'channelname' => $channelname,
            'each_channel_id' => $each_channel_id
          ])
          ->execute();

        $result = DB::select()
          ->from('message_read_check')
          ->where('username', $username)
          ->and_where('channelname', $channelname)
          ->execute()
          ->as_array();
        
        if($result){
            DB::update('message_read_check')
              ->value('read_id', $each_channel_id)
              ->where('username', $username)
              ->and_where('channelname', $channelname)
              ->execute();
        }else{
            DB::insert('message_read_check')
              ->set([
                'username' => $username,
                'channelname' => $channelname,
                'read_id' => $each_channel_id
              ])
              ->execute();
        }

        $data = DB::select()
          ->from('message')
          ->where('id', $insert[0])
          ->execute()
          ->current();
        
        return $data;
    }

    /**
     * メッセージを削除
     * @param $id int                  削除するメッセージのID
     * @param $deleted_at datetime     削除した日時
     * @return array                   削除したメッセージ
     */
    public static function delete_message($id, $deleted_at)
    {
        DB::update('message')
          ->value("deleted_at", $deleted_at)
          ->where('id', $id)
          ->execute();

        $data = DB::select()
          ->from('message')
          ->where('id', $id)
          ->execute()
          ->current();
        
        return $data;
    }

    /**
     * メッセージを編集
     * @param $id int                  削除するメッセージのID
     * @param $content string          メッセージ内容
     * @return array                   編集したメッセージデータ
     */
    public static function edit_message($id, $content)
    {
        DB::update('message')
          ->value("content", $content)
          ->where('id', $id)
          ->execute();

        $data = DB::select()
          ->from('message')
          ->where('id', $id)
          ->execute()
          ->current();
        
        return $data;
    }

    /**
     * goodを送信
     * @param $id int                  goodが押されたメッセージのID
     * @param $res_good int            good数
     * @return array                   goodが押されたメッセージデータ
     */
    public static function click_like($id, $res_good)
    {
        DB::update('message')
          ->value("res_good", $res_good)
          ->where('id', $id)
          ->execute();

        $data = DB::select()
          ->from('message')
          ->where('id', $id)
          ->execute()
          ->current();
        
        return $data;
    }

    /**
     * badを送信
     * @param $id int                  badが押されたメッセージのID
     * @param $res_bad int             bad数
     * @return array                   badが押されたメッセージデータ
     */
    public static function click_dislike($id, $res_bad)
    {
        DB::update('message')
          ->value("res_bad", $res_bad)
          ->where('id', $id)
          ->execute();

        $data = DB::select()
          ->from('message')
          ->where('id', $id)
          ->execute()
          ->current();
        
        return $data;
    }

    /**
     * ブックマークに登録
     * @param $message_id int                  ブックマークされたメッセージのID
     * @param $username string                 ブックマークしたユーザー
     * @param $bookmark_state datetime         既にブックマークされているかいないか        
     * @return array                           新しくブックマークに登録されたメッセージのデータ
     */
    public static function register_bookmark($message_id, $username, $bookmark_state)
    {
        $result =  DB::select()
          ->from('bookmark')
          ->where('username', $username)
          ->and_where('message_id', $message_id)
          ->execute()
          ->as_array();

        if(!$result) {
            $insert = DB::insert('bookmark')
              ->set([
                'username' => $username,
                'message_id' => $message_id,
              ])
              ->execute();
            
            $data = DB::select()
              ->from('message')
              ->where('id', $message_id)
              ->execute()
              ->current();

        }else{

            DB::update('bookmark')
              ->value("deleted_at", $bookmark_state)
              ->where('username', $username)
              ->and_where('message_id', $message_id)
              ->execute();
            $data = DB::select()
              ->from('message')
              ->where('id', $message_id)
              ->execute()
              ->current();
        
        }

        
        return $data;
    }

    /**
     * ブックマークから削除
     * @param $bookmark_id int                 ブックマークのID
     * @param $username string                 ブックマークしたユーザー
     * @param $bookmark_state datetime         既にブックマークされているかいないか     
     * @return array                           そのユーザーのブックマークデータ(removeが効かないため、現時点ではすべて取得)
     */
    public static function delete_bookmark($username, $bookmark_id, $bookmark_state)
    {
        DB::update('bookmark')
          ->value("deleted_at", $bookmark_state)
          ->where('username', $username)
          ->and_where('message_id', $bookmark_id)
          ->execute();
        
        $id = DB::select('message_id')
          ->from('bookmark')
          ->where('username', $username)
          ->and_where('deleted_at', '0')
          ->execute()
          ->as_array();

        $array =[];

        if($id){
            foreach($id as $i){
                $aaa = $i['message_id'];
                $array[] = $aaa;
            }

            $data = DB::select()
              ->from('message')
              ->where('id', 'in', $array)
              ->and_where('deleted_at', '0')
              ->execute()
              ->as_array();
        }else{
            $data = [];
        }
        
        return $data;
    }

    /**
     * スレッドに表示すべきコメントを取得
     * @param $chat_id int        スレッドが開かれたメッセージのID
     * @return array              コメントのデータ
     */
    public static function chat_comment($chat_id)
    {

        $data = DB::select('id', 'channelname', 'chat_id', 'commented_by', 'mention_to', 'comment_content', 'posted_at')
          ->from('comment')
          ->where('chat_id', $chat_id)
          ->execute()
          ->as_array();        
        
        return $data;
    }

    /**
     * コメントを送信
     * @param $chat_id int                  スレッドが表示されたメッセージのID
     * @param $channelname string           そのメッセージが送信されたチャンネルの名前
     * @param $commented_by string          コメントを送信したユーザー
     * @param $comment_content string       コメント内容
     * @param $mention_to string            メンション相手
     * @return array                        該当する全コメントデータ
     */
    public static function comment_post($chat_id, $channelname, $commented_by, $comment_content, $mention_to)
    {
        $insert = DB::insert('comment')
          ->set([
            'chat_id' => $chat_id,
            'channelname' => $channelname,
            'mention_to' => $mention_to,
            'commented_by' => $commented_by,
            'comment_content' => $comment_content,
          ])
          ->execute();

        $data = DB::select('id', 'channelname', 'chat_id', 'commented_by', 'mention_to', 'comment_content', 'posted_at')
          ->from('comment')
          ->where('chat_id', $chat_id)
          ->execute()
          ->as_array();
        
        return $data;
    }

    /**
     * メンション付きのコメントの既読チェック
     * @param $chat_id int                  スレッドが表示されたメッセージのID
     * @param $commented_by string          コメントを送信したユーザー
     * @param $mention_to string            メンション相手
     * @return array                        全コメントデータ
     */
    public static function read_check($chat_id, $commented_by, $mention_to)
    {
        $date = date('Y-m-d H:i:s');
        DB::update('comment')
          ->value('read_check', $date)
          ->where('id', $chat_id)
          ->and_where('mention_to', $mention_to)
          ->and_where('commented_by', $commented_by)
          ->execute();

        $mentions = DB::select('id', 'channelname', 'chat_id', 'commented_by', 'mention_to', 'comment_content', 'posted_at')
          ->from('comment')
          ->where('mention_to', $mention_to)
          ->and_where('read_check', 'is', null)
          ->execute()
          ->as_array();

        return $mentions;
    }

    /**
     * チャンネル名の横に表示するメッセージの未読チェック
     * @param $username string           現在ログインしているユーザーの名前
     * @param $channelname string        現在いるチャンネルの名前
     * @param $read_id id                そのユーザーが読んだ最新のメッセージのID(チャンネルごと)
     * @return array                     
     */
    public static function read_message($username, $channelname, $read_id)
    {
        $result = DB::select()
          ->from('message_read_check')
          ->where('username', $username)
          ->and_where('channelname', $channelname)
          ->execute()
          ->as_array();

        if($result){
            DB::update('message_read_check')
              ->value('read_id', $read_id)
              ->where('username', $username)
              ->and_where('channelname', $channelname)
              ->execute();    
        }else{

        $insert = DB::insert('message_read_check')
          ->set([
            'username' => $username,
            'channelname' => $channelname,
            'read_id' => $read_id
          ])
          ->execute();
        }

        $data = DB::select()
          ->from('message_read_check')
          ->execute()
          ->as_array();

        return $data;
    }

}