<style>
details {
  padding-left: 20px;

}

details > summary {
  padding-bottom: 10px;
  cursor: pointer;
}

details[open] > summary {
}

</style>

# 機能一覧

<details>
<summary>ログイン画面</summary>

<details>
<summary>ログアウト</summary>

## views/auth/index.php

```html
<h1>ログイン画面</h1>
<form method="POST" action="/auth/login">
  username:<input type="text" name="username" /><br />
  password:<input type="text" name="password" /><br />
  <input type="submit" value="送信" />
</form>
```

## controller/auth.php

```php
public function action_login()
{

    if (Input::post()) {

        $username = Input::post('username');
        $password = Input::post('password');

        if (Auth::login($username, $password)) {

            View::set_global('usr', $username);
            Response::redirect('channel/index');

        } else {
            echo "ログインに失敗しました";
            Response::redirect('auth/index');

        }
    }
    return View::forge('auth/index');
}
```

</details>

<details>
<summary>サインアップ</summary>

## views/auth/sign_up.php

```html
<h1>ログイン画面</h1>
<form method="POST" action="/auth/index">
  username:<input type="text" name="username" /><br />
  password:<input type="text" name="password" /><br />
  Email:<input type="text" name="mail" /><br />

  <input type="submit" value="送信" />
</form>
```

## controller/auth.php

```php
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

            DB::insert('profile')->set([
                'username' => "$username",
            ])->execute();

            return View::forge('auth/index');
        }
        return View::forge('auth/index');

    }

public function action_signup()
{
    return View::forge('auth/sign_up');
}
```

</details>
</details>

<!-- ================================== -->

<details>
<summary>チャンネル一覧画面</summary>

<details>
<summary>チャンネル一覧</summary>

## controller/channel.php

```php
public function action_index()
{
    $data['id_info'] = Auth::get_user_id();
    $loginUser = Auth::get_screen_name();
    $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

    if(count($channel_key) != 0) {
        $channel = DB::select()->from('channel')
        ->where_open()
        ->where('open', 0)->and_where('deleted_at', '0')
        ->where_close()
        ->or_where_open()
        ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
        ->or_where_close()
        ->execute()->as_array();
    } else {
        $channel = DB::select()->from('channel')
        ->where('open', 0)->and_where('deleted_at', '0')
        ->execute()->as_array();
    };

    $private = DB::select()->from('channel')->where('channelname', $loginUser)->execute()->as_array();

    $all_channels = array_merge($channel, $private);
    $data['user'] = Arr::get(Auth::get_user_id(),1);
    $data['loginUser'] = $loginUser;
    $data['channelkey'] = $channel_key;
    $data['notification'] = DB::select()->from('comment')
    ->where('mention_to', $loginUser)->and_where('read_check', '0')
    ->execute()->as_array();
    // $data['loginUser'] = $loginUser;

    $read_check = [];
    $check = [];
    $test = [];

    $channel_name = array_column($all_channels, "channelname");

    foreach ($channel_name as $ch){
        $channelid = DB::select('each_channel_id')
        ->from('message')
        ->order_by('each_channel_id', 'desc')
        ->where('channelname', $ch)
        ->execute()->current();

        $msg_read_check = DB::select('channelname', 'read_id')
        ->from('message_read_check')
        ->where('channelname', $ch)
        ->and_where('username', $loginUser)
        ->execute()->current();

        if($channelid){
            $aaa = $channelid['each_channel_id'];
        }else{
            $aaa = '0';
        };

        if($msg_read_check){
            $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => $msg_read_check['read_id']];
        }else{
            $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => '0'];
        };
    };

    foreach ($read_check as $rc){
        $value = intval($rc -> id) - intval($rc -> read_id);
        $check[] = ['read_id' => $value];
    };

    foreach ($all_channels as $index => $aaa){

        $test[] = array_merge($aaa, $check[$index]);
    }


    $data['test'] = $test;

    // $data['data'] = array_merge($channel, $private);

    return View::forge('channel/channel', $data);

}
```

## views/channel.php

```javascript
let myViewModel = {
  channels: ko.observableArray(test),
  addChannelForm: ko.observable(false),
  keyIcon: function (isOpen) {
    let locked;
    if (isOpen.owner == 'dm') {
      locked = '👥';
    } else if (isOpen.open == '1' && isOpen.private == '1') {
      locked = '👤';
    } else if (isOpen.open == '1') {
      locked = '🔒';
    } else {
      locked = '📖';
    }
    return locked;
  },
  readOrNot: function (value) {
    // console.log(value);
    let read;
    if (value.read_id == '0') {
      read = '';
    } else {
      read = '+' + value.read_id;
    }
    return read;
  },
};
```

</details>

<details>
<summary>チャンネル移動</summary>

## views/channel/channel.php

```javascript
<div data-bind="foreach: channels">
    <span data-bind="text: $parent.keyIcon($data)"></span>
    <a id="link" href="#" data-bind="click: $parent.moveToChannel, text: channelname, value: channelname"></a>

    <span data-bind="text: $parent.readOrNot($data)" style="color: red"></span>
    <br>
</div>
```

```javascript
<script>
    myViewModel.moveToChannel = function(channel) {
        let link = document.getElementById('link');
        let url = '<?php echo Uri::create('message/index/'); ?>'+channel['channelname'];
        link.setAttribute('href', url);
        window.location.href = url;
    };
<script>

```

</details>

<details>
<summary>チャンネル追加</summary>

## views/channel/channel.php

```html
<div>
  <p data-bind="click: showAddChannelForm">チャンネルを追加</p>
  <form
    method="POST"
    action=""
    name="channel"
    data-bind="visible: addChannelForm"
  >
    チャンネル名:<input
      type="text"
      id="addChannel"
      name="channel"
      placeholder="登録するチャンネル名を入力してください。"
    />
    チャンネルの公開範囲：<select name="number">
      <option value="1">public</option>
      <option value="2">private</option></select
    ><br />
    <button data-bind="click: addChannel">送信</button>
  </form>
</div>
```

```javascript
myViewModel.showAddChannelForm = function() {
    myViewModel.addChannelForm(!myViewModel.addChannelForm());
};

myViewModel.addChannel = function() {

    event.preventDefault();

    let channel_visibility = document.channel.number;
    let num = channel_visibility.selectedIndex;
    let username = '<?php echo $loginUser; ?>';
    let channelname = document.getElementById("addChannel").value;
    console.log(num);
    console.log(channelname);
    let formData = {
        'open': num,
        'owner': username,
        'channelname': channelname,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);
    // console.log(myViewModel.chats()[1]);
    $.ajax({
        url: '<?php echo Uri::create('register/register.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("成功");
        console.log("===========================================");
        console.log(data);
        myViewModel.addChannelForm(!myViewModel.addChannelForm());
        myViewModel.channels(data);

    }).fail(function() {
        alert("失敗");
    });

};
```

## controller/register.php

```php
public function post_register()
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
    $owner = Input::post('owner');
    $open = Input::post('open');

    $insert = DB::insert('channel')->set([
        'channelname' => "$channelname",
        'open' => $open,
        'owner' => "$owner"
    ])->execute();

    DB::insert('channel_secret_key')->set([
        'channel_id' => $insert[0],
        'username' => $owner,
    ])->execute();


    $channel_key = DB::select('channel_id')->from('channel_secret_key')->where('username', $loginUser)->execute()->as_array();

    if(count($channel_key) != 0) {
        $channel = DB::select()->from('channel')
        ->where_open()
        ->where('open', 0)->and_where('deleted_at', '0')
        ->where_close()
        ->or_where_open()
        ->where('id', 'in', $channel_key)->and_where('deleted_at', '0')->and_where('open', '1')
        ->or_where_close()
        ->execute()->as_array();
    } else {
        $channel = DB::select()->from('channel')
        ->where('open', 0)->and_where('deleted_at', '0')
        ->execute()->as_array();
    };

    // $public = DB::select()->from('channel')->where('open', "0")->and_where('deleted_at', '0')->execute()->as_array();
    $private = DB::select()->from('channel')->where('channelname', $owner)->execute()->as_array();

    $all_channels = array_merge($channel, $private);


    $read_check = [];
    $check = [];
    $data = [];

    $channel_name = array_column($all_channels, "channelname");

    foreach ($channel_name as $ch){
        $channelid = DB::select('each_channel_id')
        ->from('message')
        ->order_by('each_channel_id', 'desc')
        ->where('channelname', $ch)
        ->execute()->current();

        $msg_read_check = DB::select('channelname', 'read_id')
        ->from('message_read_check')
        ->where('channelname', $ch)
        ->and_where('username', $loginUser)
        ->execute()->current();

        if($channelid){
            $aaa = $channelid['each_channel_id'];
        }else{
            $aaa = '0';
        };

        if($msg_read_check){
            $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => $msg_read_check['read_id']];
        }else{
            $read_check[] = (object)['id' => $aaa, 'channelname' => $ch, 'read_id' => '0'];
        };
    };


    foreach ($read_check as $rc){
        $value = intval($rc -> id) - intval($rc -> read_id);
        $check[] = ['read_id' => $value];
    };

    foreach ($all_channels as $index => $aaa){

        $data[] = array_merge($aaa, $check[$index]);
    }

    return $this->response($data);
}
```

</details>

<details>

<summary>お知らせ</summary>

## views/channel/channel.php

```html
<p>お知らせ一覧</p>
<div data-bind="foreach: notification">
  <div style="border: solid black 1px">
    FROM: <span data-bind="text: commented_by"></span><br />
    <span data-bind="text: comment_content"></span><br />
    <a
      href=""
      id="link2"
      data-bind="click: $parent.moveToChannelViaNotification"
      >チャンネルへ移動</a
    >
  </div>
</div>
```

```javascript
let notification =
    <?php
    $json=json_encode($notification,JSON_PRETTY_PRINT);
    echo $json;
?>;

myViewModel.moveToChannelViaNotification = function(channel) {
    // event.preventDefault();
    // console.log(channel.channelname);
    let link = document.getElementById('link2');
    let id = channel.channelname;
    let url = '<?php echo Uri::create('message/index/'); ?>'+channel.channelname;
    link.setAttribute('href', url);
    window.location.href = url;

};
```

## controllers/channel.php

```php
public function action_index() {
    $data['notification'] = DB::select()->from('comment')
    ->where('mention_to', $loginUser)->and_where('read_check', '0')
    ->execute()->as_array();
}
```

</details>

</details>

<!-- ================================== -->

<details>

<summary>ブックマーク画面</summary>

<details>

<summary>ブックマーク一覧</summary>

## views/message/bookmark.php

```html
<div data-bind="foreach: message">
  <span
    style="padding: 1rem; font-size: 20px"
    data-bind="text: username, value: username"
  ></span>
  <span data-bind="text: posted_at"></span><br />
  <div style="border: solid black 1px; padding: 1rem">
    <span data-bind="text: content, value: content"></span>
  </div>
  <a href="">ブックマークから削除する</a>
  <br />
</div>
```

```javascript
let obj =
    <?php
    $json=json_encode($data,JSON_PRETTY_PRINT);
    echo $json;
    ?>;
    console.log(obj);

let myViewModel = {
    message: ko.observableArray(obj)
};
```

## controller/bookmark.php

```php
public function action_index()
{
    $user = Arr::get(Auth::get_user_id(),1);
    $loginUser = Auth::get_screen_name();
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

    $data['data'] = $bookmark;


    return View::forge('message/bookmark', $data);

    // print_r($user);
}
```

</details>

</details>

<!-- ================================== -->

<details>

<summary>プロフィール画面</summary>

<details>

<summary>プロフィールページ</summary>

## views/profile/profile.php

```html
<div>
  <p data-bind="text: username"></p>
  <p data-bind="text: self_introduction"></p>
  <p data-bind="text: url"></p>
</div>
<div>
  <a href="" data-bind="click: moveToDM, visible: isVisible()"
    >DMはこちらのリンクから</a
  ><br />
  <a href="" data-bind="click: editProf, visible: visible()"
    >プロフィールを編集する</a
  >

  <form action="" method="post" data-bind="visible: showForm">
    <textarea
      type="text"
      id="content"
      placeholder="ここに文章を入力してください"
      data-bind="text: editContent"
    ></textarea>
    <input
      type="text"
      id="url_link"
      placeholder="URL"
      data-bind="text: editUrl"
    />
    <button data-bind="click: submitNewProf">完了</button>
  </form>
</div>
```

```javascript
let obj =
    <?php
    $json=json_encode($data,JSON_PRETTY_PRINT);
    echo $json;
    ?>;

let myViewModel = {
    username: ko.observable(obj.username),
    self_introduction: ko.observable(obj.self_introduction),
    url: ko.observable(obj.url_link),
    showForm: ko.observable(false),
    editContent: ko.observable(obj.self_introduction),
    editUrl: ko.observable(obj.url_link),
    isVisible: function() {
        let visible;
        if(obj.username != '<?php echo $loginUser ?>') {
            visible = true;
        }else{
            visible = false;
        };
        return visible;
    },
    visible: function() {
        let visible;
        if(obj.username == '<?php echo $loginUser ?>') {
            visible = true;
        }else{
            visible = false;
        };
        return visible;
    },
};
```

## controller/profile.php

```php
public function action_index()
{
    $user = Arr::get(Auth::get_user_id(),1);
    $loginUser = Auth::get_screen_name();
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

    $data['data'] = $bookmark;


    return View::forge('message/bookmark', $data);

    // print_r($user);
}
```

</details>

<details>

<summary>プロフィール編集</summary>

## view/profile/profile.php

```html
<a href="" data-bind="click: editProf, visible: visible()"
  >プロフィールを編集する</a
>

<form action="" method="post" data-bind="visible: showForm">
  <textarea
    type="text"
    id="content"
    placeholder="ここに文章を入力してください"
    data-bind="text: editContent"
  ></textarea>
  <input
    type="text"
    id="url_link"
    placeholder="URL"
    data-bind="text: editUrl"
  />
  <button data-bind="click: submitNewProf">完了</button>
</form>
```

```javascript
myViewModel.editProf = function() {

event.preventDefault();
myViewModel.showForm(!myViewModel.showForm());

};

myViewModel.submitNewProf = function() {
event.preventDefault();
let content = document.getElementById("content").value;
if(content == ""){
    content = obj.self_introduction;
}
let url_link = document.getElementById("url_link").value;
if(url_link == ""){
    url_link = obj.url_link;
}

let formData = {
    'username': '<?php echo $loginUser ?>',
    'content': content,
    'url_link': url_link,
    'cc_token': fuel_csrf_token()
};
console.log(formData);

$.ajax({
    url: '<?php echo Uri::create('edit/edit.json'); ?>',
    type: 'POST',
    cache: false,
    dataType : 'json',
    data: formData,

}).done(function(data) {
    // alert("成功");
    console.log("===========================================");
    console.log(data);

    alert("編集が完了しました。")

    myViewModel.self_introduction(data.self_introduction);
    myViewModel.url(data.url_link);
    myViewModel.self_introduction(myViewModel.self_introduction());
    myViewModel.url(myViewModel.url());

    myViewModel.showForm(!myViewModel.showForm());


}).fail(function() {
    alert("失敗");
});
```

## controller/edit.php

```php
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


    $result = DB::update('profile')->set([
        'self_introduction' => $content,
        'url_link' => $url_link
    ])
        ->where('username', $username)
        ->execute();

    $data = DB::select()->from('profile')
        ->where('username', $username)->and_where('deleted_at', '0')
        ->execute()->current();

    return $this->response($data);

}
```

</details>

<details>

<summary>ダイレクトメッセージ</summary>

## view/profile/profile.php

```html
<a href="" data-bind="click: editProf, visible: visible()"
  >プロフィールを編集する</a
>
```

```javascript
myViewModel.moveToDM = function() {
    event.preventDefault();
    let channelname = obj.username + '-' + '<?php echo $loginUser ?>';
    let profile_user = obj.username;
    let login_user = '<?php echo $loginUser ?>';

    let formData = {
        'channelname': channelname,
        'profile_user': profile_user,
        'login_user': login_user,
        'cc_token': fuel_csrf_token()
    };

    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('register/DM_create.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("DMに移動します。");
        console.log("===========================================");
        console.log(data);
        let url = '<?php echo Uri::create('message/index/'); ?>'+data.channelname;
        window.location.href = url;

    }).fail(function() {
        alert("失敗");
    });
};
```

## controller/register.php

```php
public function post_DM_create()
{

    // トークンチェック
    if (!\Security::check_token()) :
        $res = array(
        'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
    );

    return $this->response($res);

    endif;

    $channelname = Input::post('channelname');
    $username1 = Input::post('profile_user');
    $username2 = Input::post('login_user');

    $result = DB::select('id')->from('channel')
        ->where('channelname', $username1.'-'.$username2)
        ->or_where('channelname', $username2.'-'.$username1)
        ->execute()->current();


    if($result){
        $data = DB::select('channelname')->from('channel')
            ->where('id', $result['id'])
            ->execute()->current();

    }else{
        $insert = DB::insert('channel')->set([
            'channelname' => "$channelname",
            'open' => '1',
            'owner' => "dm"
        ])->execute();

        DB::insert('channel_secret_key')->set([
            'channel_id' => $insert[0],
            'username' => $username1

        ])->execute();

        DB::insert('channel_secret_key')->set([
            'channel_id' => $insert[0],
            'username' => $username2

        ])->execute();

        $data = DB::select('channelname')->from('channel')
            ->where('id', $insert[0])
            ->execute()->current();

    }

    return $this->response($data);

}
```

</details>

</details>

<!-- ================================== -->

<details>

<summary>メッセージ画面</summary>

<details>

<summary>メッセージ一覧</summary>

## views/message/index.php

```html
<div id="message" data-bind="foreach: message" style="margin: 3rem;">
  <div
    data-bind="visible: $parent.isVisible($data)"
    style="padding-top: 3rem; border-top: solid black 1px;"
  >
    <span
      id="link"
      style="padding: 1rem; font-size: 20px"
      data-bind="text: username, value: username, click: $parent.moveToProf"
    ></span>
    <span data-bind="text: posted_at"></span><br />
    <div style="border: solid black 1px; padding: 1rem">
      <span
        style="white-space: pre-line;"
        data-bind="text: content, value: content"
      ></span>
    </div>
    <span>👍</span
    ><a
      href="#"
      style="padding-left: 5px"
      data-bind="click: $parent.postGood, text: res_good, value: res_good"
    ></a>
    <span>👎</span
    ><a
      href="#"
      style="padding-left: 5px"
      data-bind="click: $parent.postBad, text: res_bad, value: res_bad"
    ></a>
    <a
      href="#"
      data-bind="click: $parent.editChat, text: $root.btn_edit($data)"
      style="padding-left: 1rem"
      >編集</a
    >
    <a
      href="#"
      data-bind="click: $parent.deleteChat, text: $root.btn_delete($data)"
      >削除</a
    >
    <a
      href="#"
      data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)"
      style="padding-left: 15px"
    ></a
    ><br />
    <br />
    <details id="detail">
      <summary data-bind="click: $parent.showComments">スレッドを表示</summary>
      <div data-bind="foreach: $parent.chats">
        <span data-bind="text: commented_by"></span><br />
        <span
          style="color: blue"
          data-bind="text: $root.mention_to($data)"
        ></span>
        <span data-bind="text: comment_content"></span><br /><br />
      </div>
      <a href="" data-bind="click: $parent.comment">コメントを追加する</a>
    </details>
  </div>
</div>
```

```javascript

```

##

```

```

</details>

<details>

<summary>メッセージ送信</summary>

## views/message/index.php

```html
<form action="" method="post" data-bind="visible: showForm">
  <textarea
    type="text"
    id="content1"
    data-bind='value: form1, valueUpdate: "afterkeydown"'
    placeholder="ここにメッセージを入力してください"
  ></textarea>
  <button data-bind="click: submitMessage">送信</button>
</form>
```

```javascript
myViewModel.submitMessage = function (){
    event.preventDefault();
    let username = '<?php echo $loginUser; ?>';
    let channelname = '<?php echo $channelname; ?>';
    let content = document.getElementById("content1").value;
    let each_channel_id = '<?php echo $current_message ?>';
    let formData = {
        'username': username,
        'content': content,
        'channelname': channelname,
        'each_channel_id': Number(each_channel_id) + 1,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('chat/chat_post.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        // console.log(data);

        myViewModel.message.push(data);
        myViewModel.message(myViewModel.message());
        myViewModel.form1("");

    }).fail(function() {
        alert("失敗");
    });
};
```

##

```

```

</details>

<details>

<summary>メッセージ編集・削除</summary>

## views/message/index.php

```html
<a
  href="#"
  data-bind="click: $parent.editChat, text: $root.btn_edit($data)"
  style="padding-left: 1rem"
  >編集</a
>
<a href="#" data-bind="click: $parent.deleteChat, text: $root.btn_delete($data)"
  >削除</a
>
<a
  href="#"
  data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)"
  style="padding-left: 15px"
></a
><br />

<form action="" method="post" data-bind="visible: showEditForm">
  <span>メッセージの編集中です</span>
  <a href="#" data-bind="click: editStop">取消</a><br />
  <textarea
    type="text"
    id="content2"
    data-bind='value: form2, valueUpdate: "afterkeydown"'
  ></textarea>
  <button data-bind="click: submitNewMessage">送信</button>
</form>
```

```javascript
myViewModel.editChat = function(msg) {

    editChatId = msg['id'];
    // console.log(editChatId);
    event.preventDefault();
    myViewModel.form2(msg['content']);
    myViewModel.showCommentForm(false);
    myViewModel.showEditForm(true);
    myViewModel.showForm(false);

};

myViewModel.submitNewMessage = function() {
    event.preventDefault();
        let id = editChatId;
        let content = document.getElementById("content2").value;
        // let content = $('input[name=content2]').val();
        let channelname = '<?php echo $channelname; ?>';

        let formData = {
            'id': id,
            'channelname': channelname,
            'content': content,
            'cc_token': fuel_csrf_token()
        };
        console.log(formData);

        $.ajax({
            url: '<?php echo Uri::create('chat/chat_edit.json'); ?>',
            type: 'POST',
            cache: false,
            dataType : 'json',
            data: formData,

        }).done(function(data) {
            // alert("成功");
            console.log("===========================================");
            console.log([data]);

            let index = getIndex(editChatId, obj, 'id');
            myViewModel.message()[index] = data;
            myViewModel.message(myViewModel.message());

            myViewModel.form2("");
            myViewModel.showEditForm(false);
            myViewModel.showForm(true);
            alert("メッセージの編集が完了しました。")

        }).fail(function() {
            alert("失敗");
        });


}

myViewModel.editStop = function() {
    myViewModel.form2("");
    myViewModel.showEditForm(false);
    myViewModel.showCommentForm(false);
    myViewModel.showForm(true);
    alert("メッセージの編集を中断しました。")

}

myViewModel.deleteChat = function(msg) {

    event.preventDefault();
    let id = msg['id'];
    let channelname = '<?php echo $channelname; ?>';
    console.log(msg['id']);

    let formData = {
        'id': id,
        'channelname': channelname,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('chat/chat_delete.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        console.log(data);

        let index = getIndex(id, obj, 'id');
        myViewModel.message.remove(myViewModel.message()[index]);
        myViewModel.message(myViewModel.message());

        // myViewModel.message(data);
        alert("メッセージを削除しました。")

    }).fail(function() {
        alert("失敗");
    });


};
```

##

```

```

</details>

<details>

<summary>👍or👎</summary>

##

```html
<span>👍</span
><a
  href="#"
  style="padding-left: 5px"
  data-bind="click: $parent.postGood, text: res_good, value: res_good"
></a>
<span>👎</span
><a
  href="#"
  style="padding-left: 5px"
  data-bind="click: $parent.postBad, text: res_bad, value: res_bad"
></a>
```

##

```javascript
myViewModel.postGood = function(msg) {
    event.preventDefault();
    let goodId = msg['id'];
    let goodCount = Number(msg['res_good']) + 1;
    let channelname = '<?php echo $channelname; ?>';

    let id = goodId;

    let formData = {
        'id': id,
        'channelname': channelname,
        'res_good': goodCount,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('chat/post_good.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        let index = getIndex(goodId, obj, 'id');
        myViewModel.message()[index] = data;
        myViewModel.message(myViewModel.message());


    }).fail(function() {
        alert("失敗");
    });

}

// post bad section

myViewModel.postBad = function(msg) {
    event.preventDefault();
    let badId = msg['id'];
    let badCount = Number(msg['res_bad']) + 1;
    let channelname = '<?php echo $channelname; ?>';

    let id = badId;

    let formData = {
        'id': id,
        'channelname': channelname,
        'res_bad': badCount,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('chat/post_bad.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        console.log(data);
        let index = getIndex(badId, obj, 'id');
        myViewModel.message()[index] = data;
        myViewModel.message(myViewModel.message());

    }).fail(function() {
        alert("失敗");
    });

}
```

##

```

```

</details>

<details>

<summary>ブックマーク</summary>

##

```html
<a
  href="#"
  data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)"
  style="padding-left: 15px"
></a
><br />
```

##

```javascript
myViewModel.bookmark = function (msg){
    event.preventDefault();
    let id;
    let url;
    let bookmark;
    if(bookmarks.indexOf(msg.id) != -1){
        bookmark = "delete";
    }else{
        bookmark = "add";
    };
    let bookmark_state;
    let alertmsg;
    console.log(bookmark);

    if(bookmark == "add") {
        bookmark_state = "0";
        id = msg.id;
        url = '<?php echo Uri::create('chat/bookmark_create.json'); ?>';
        alertmsg = "ブックマークに登録しました。";

    }else{
        bookmark_state = '<?php echo date('Y-m-d H:i:s') ?>';
        id = msg.id;
        url = '<?php echo Uri::create('chat/bookmark_delete.json'); ?>';
        alertmsg = "ブックマークから削除しました。";
    }
    // let bookmark = Math.abs(Number(msg['bookmark']) - 1);

    let formData = {
        'bookmark_state': bookmark_state,
        'id': id,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        console.log(data);

        myViewModel.bookmarks.push(data['message_id']);
        alert(alertmsg);

    }).fail(function() {
        alert("失敗");
    });
};
```

##

```

```

</details>

<details>

<summary>チャンネル編集</summary>

##

```html
<form
  method="POST"
  action=""
  name="inviteUser"
  data-bind="visible: channelSettingsFormVisibility"
  style="color: black"
>
  チャンネル名を変更:
  <input
    type="text"
    id="newChannelname"
    placeholder="新しいチャンネル名を入力してください"
  />
  <button data-bind="click: editChannelname">送信</button>
</form>
<form
  method="POST"
  action=""
  name="channelSettings"
  data-bind="visible: channelSettingsFormVisibility"
  style="color: black"
>
  チャンネルの公開範囲:<select name="number">
    <option value="1">public</option>
    <option value="2">private</option>
  </select>
  <button data-bind="click: editChannel">完了</button>
</form>
<form
  method="POST"
  action=""
  name="inviteUser"
  data-bind="visible: channelSettingsFormVisibility"
  style="color: black"
>
  ユーザーを招待する:
  <select
    data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'"
  ></select>
  <button data-bind="click: inviteUser">送信</button>
</form>
```

##

```javascript
myViewModel.showChannelSettings = function() {
        myViewModel.channelSettingsFormVisibility(!myViewModel.channelSettingsFormVisibility());
    };

myViewModel.editChannelname = function() {
    event.preventDefault();
    let newChannelname = document.getElementById("newChannelname").value;
    let channelname = '<?php echo $channelname ?>';

    let formData = {
        'newChannelname': newChannelname,
        'channelname': channelname,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);

    $.ajax({
        url: '<?php echo Uri::create('register/edit_channelname.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("新しいチャンネルに移動します。");
        console.log("===========================================");
        console.log(data);
        let url = '<?php echo Uri::create('message/index/'); ?>'+data;
        window.location.href = url;

    }).fail(function() {
        alert("失敗");
    });
};

myViewModel.editChannel = function() {

    event.preventDefault();

    let channel_visibility = document.channelSettings.number;
    let num = channel_visibility.selectedIndex;
    let formData = {
        'open': num,
        'channel_id': channelData.id,
        'cc_token': fuel_csrf_token()
    };
    $.ajax({
        url: '<?php echo Uri::create('register/edit.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("成功");
        console.log("===========================================");
        console.log(data);

    }).fail(function() {
        alert("失敗");
    });

    };

myViewModel.inviteUser = function() {

    event.preventDefault();

    let invite_user = myViewModel.selectedUser();
    let formData = {
        'invited_user': invite_user,
        'channel_id': channelData.id,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);
    console.log(myViewModel.chats()[1]);
    $.ajax({
        url: '<?php echo Uri::create('register/invite.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("成功");
        console.log("===========================================");
        console.log(data);
        // myViewModel.channels(data);

    }).fail(function() {
        alert("失敗");
    });

};
```

##

```

```

</details>

<details>

<summary>コメント(スレッド)</summary>

##

```html
<details id="detail">
  <summary data-bind="click: $parent.showComments">スレッドを表示</summary>
  <div data-bind="foreach: $parent.chats">
    <span data-bind="text: commented_by"></span><br />
    <span style="color: blue" data-bind="text: $root.mention_to($data)"></span>
    <span data-bind="text: comment_content"></span><br /><br />
  </div>
  <a href="" data-bind="click: $parent.comment">コメントを追加する</a>
</details>

<form action="" method="post" data-bind="visible: showCommentForm">
  <span>コメントを入力中です</span>
  <a href="#" data-bind="click: editStop">取消</a><br />
  メンション:<select
    data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'"
  ></select
  ><br />
  <textarea
    type="text"
    id="comment"
    placeholder="コメントを入力してください"
  ></textarea>
  <button data-bind="click: submitComment">送信</button>
</form>
```

##

```javascript
myViewModel.submitComment = function() {
    event.preventDefault();
    let mention_to;
    if(typeof myViewModel.selectedUser()=== "undefined"){
        mention_to = "all";
    }else{
        mention_to = myViewModel.selectedUser();
    };
    // console.log(mention_to);

    let username = '<?php echo $loginUser; ?>';
    let channelname = '<?php echo $channelname; ?>';
    let content = document.getElementById("comment").value;
    let formData = {
        'chat_id': message_id,
        'channelname': channelname,
        'mention_to': mention_to,
        'commented_by': username,
        'comment_content': content,
        'cc_token': fuel_csrf_token()
    };
    console.log(formData);
    // console.log(myViewModel.chats()[1]);
    $.ajax({
        url: '<?php echo Uri::create('chat/comment_post.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        alert("成功");
        console.log("===========================================");
        console.log(data);

        myViewModel.chats(data);
        myViewModel.chats(myViewModel.chats());
        myViewModel.showCommentForm(false);
        myViewModel.showEditForm(false);
        myViewModel.showForm(true);

    }).fail(function() {
        alert("失敗");
    });

};

// show comments section
myViewModel.showComments = function(details) {
    // document.getElementById("detail").removeAttribute("open");
    // document.getElementById("detail").setAttribute("open", "false");
    let chats=[];
    myViewModel.chats(chats);
    myViewModel.chats(myViewModel.chats());

    console.log(details);
    let chat_id = details.id;
    let formData = {
        'chat_id': chat_id,
        'cc_token': fuel_csrf_token()
    };
    $.ajax({
        url: '<?php echo Uri::create('chat/chat_comment.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        // alert("成功");
        console.log("===========================================");
        // console.log(data);

        myViewModel.chats(data);
        myViewModel.chats(myViewModel.chats());

    }).fail(function() {
        alert("失敗");
    });
    return true;

}

let message_id;

myViewModel.comment = function(msg) {
    console.log(msg);
    message_id = msg['id'];

    event.preventDefault();
    myViewModel.showCommentForm(true);
    myViewModel.showEditForm(false);
    myViewModel.showForm(false);


```

##

```

```

</details>

<details>

<summary>メッセージフィルター</summary>

##

```html
<p style="color: black">フィルター🔍</p>
    <form>
        <input type="text" data-bind='value: stringValue, valueUpdate: "afterkeydown"' placeholder="検索したい文字列を入力してください">
    </form>
</div>
```

##

```

```

##

```

```

</details>

<details>

<summary>画面同期</summary>

##

```javascript
// 画面同期
let now_data = obj;

const dataCheck = function() {
    let formData = {
        'channelname': '<?php echo $channelname ?>',
    };

    $.ajax({
        url: '<?php echo Uri::create('chat/check_data.json'); ?>',
        type: 'POST',
        cache: false,
        dataType : 'json',
        data: formData,

    }).done(function(data) {
        console.log("===========================================");
        console.log(data);

        if(JSON.stringify(data) === JSON.stringify(now_data)){
            console.log(true);
        }else{
            console.log(false);
            now_data = data;
            myViewModel.message(now_data);
            myViewModel.message(myViewModel.message());

        };


    }).fail(function() {
        alert("失敗");
    });
}

setInterval(dataCheck, 1000);
```

##

```

```

##

```

```

</details>

</details>

<!-- ================================== -->

<!-- <details>

<summary></summary>

##

```

```

##

```

```

##

```

```

<details>

<summary></summary>

##

```

```

##

```

```

##

```

```

</details>

<details>

<summary></summary>

##

```

```

##

```

```

##

```

```

</details>

</details> -->

<!-- ================================== -->
