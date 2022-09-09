<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.8.3.js" integrity="sha256-dW19+sSjW7V1Q/Z3KD1saC6NcE5TUIhLJzJbrdKzxKc=" crossorigin="anonymous"></script>
    <?php echo \Security::js_fetch_token(); ?>
    <?php echo Asset::css('style.css'); ?>
</head>

<body id='container'>

<header style="color: white; background-color: #222222; top: 0; height: 3rem; padding-left: 1rem">
    <h1><?php echo $channelname ?> <?php echo $loginUser ?></h1>
    <nav style="display: inline-block">
        <a href="/channel/index">チャンネル一覧</a>
        <a href="/bookmark/index">ブックマーク一覧</a>
        <a href="" data-bind="click: showChannelSettings, text: channelSettings, visible: channelSettingsVisibility()"></a>
        <a href="/auth/logout">ログアウト</a>

    </nav>

    <form method="POST" action="" name="channelSettings" data-bind="visible: channelSettingsFormVisibility" style="color: black">
        チャンネルの公開範囲:<select name="number">
            <option value="1">public</option>
            <option value="2">private</option>
        </select><br>
        <button data-bind="click: editChannel">完了</button>
    </form>
    <form method="POST" action="" name="inviteUser" data-bind="visible: channelSettingsFormVisibility" style="color: black">
    ユーザーを招待する:
            <select data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'">
            </select><br>
        <button data-bind="click: inviteUser">送信</button>
    </form>



    <p style="color: black">フィルター🔍</p>
        <form>
            <input type="text" data-bind='value: stringValue, valueUpdate: "afterkeydown"' placeholder="検索したい文字列を入力してください">
        </form>
    </div>

</header>
    <main>

    <div style="width: 100%;">


    <div id="message" data-bind="foreach: message" style="margin: 3rem;">
        <div data-bind="visible: $parent.isVisible($data)" style="padding-top: 3rem; border-top: solid black 1px;">
            <span style="padding: 1rem; font-size: 20px" data-bind="text: username, value: username"></span> 
            <span data-bind="text: posted_at"></span><br>
            <div style="border: solid black 1px; padding: 1rem">
                <span style="white-space: pre-line;" data-bind="text: content, value: content"></span>
            </div>
            <span>👍</span><a href="#" style="padding-left: 5px" data-bind="click: $parent.postGood, text: res_good, value: res_good"></a>
            <span>👎</span><a href="#" style="padding-left: 5px" data-bind="click: $parent.postBad, text: res_bad, value: res_bad"></a>
            <a href="#" data-bind="click: $parent.editChat" style="padding-left: 1rem">編集</a>
            <a href="#" data-bind="click: $parent.deleteChat">削除</a>
            <a href="#" data-bind="click: $parent.bookmark, text: $parent.stateBookmark" style="padding-left: 20px"></a><br>
            <br>
            <details id="detail">
            <summary data-bind="click: $parent.showComments" >スレッドを表示</summary>    
            <div data-bind="foreach: $parent.chats">
            <span data-bind="text: commented_by"></span><br>
            <span data-bind="text: comment_content"></span><br><br>
            </div>
            <a href="" data-bind="click: $parent.comment">コメントを追加する</a>

            </details>
        </div>

    </div>

    <br>



    <div style="position: fixed; bottom: 0px; width: 100%;">
    <form action="" method="post" data-bind="visible: showForm"  >
        <textarea type="text" id="content1" data-bind='value: form1, valueUpdate: "afterkeydown"' placeholder="ここにメッセージを入力してください"></textarea>
        <button data-bind="click: submitMessage">送信</button>
    </form>

    <form action="" method="post" data-bind="visible: showEditForm">
        <span>メッセージの編集中です</span> <a href="#" data-bind="click: editStop">取消</a><br>
        <textarea type="text" id="content2" data-bind='value: form2, valueUpdate: "afterkeydown"'></textarea>
        <button data-bind="click: submitNewMessage" >送信</button>
    </form>

    <form action="" method="post" data-bind="visible: showCommentForm">
        <span>コメントを入力中です</span> <a href="#" data-bind="click: editStop">取消</a><br>
        <textarea type="text" id="comment" placeholder="コメントを入力してください"></textarea>
        <button data-bind="click: submitComment">送信</button>
    </form>

    </div>

    </main>
</body>

<script type="text/javascript">
    let channelData = 
        <?php
        $json=json_encode($channelData,JSON_PRETTY_PRINT);
        echo $json;
    ?>;
    
    let usersObj = 
        <?php
        $json=json_encode($users,JSON_PRETTY_PRINT);
        echo $json;
    ?>;
    // console.log(usersObj);
    let users = usersObj.map(function(item) {
        return item.username
    });
    console.log(users);

    function getIndex(value, arr, prop) {
        for(let i = 0; i < arr.length; i++) {
            if(arr[i][prop] === value) {
                return i;
            }
        }
        return -1; //値が存在しなかったとき
    };

    let obj = 
        <?php
        $json=json_encode($data,JSON_PRETTY_PRINT);
        echo $json;
        ?>;
        // console.log(obj);
    
    let comments = 
        <?php
        $json=json_encode($comment,JSON_PRETTY_PRINT);
        echo $json;
        ?>;

    let myViewModel = {
        stringValue: ko.observable(""),
        showComments: ko.observable(""),
        channelSettings: ko.observable("チャンネル設定"),
        channelSettingsFormVisibility: ko.observable(false),
        channelSettingsVisibility: function() {
            let visible;
            if(channelData.owner == '<?php echo $loginUser ?>') {
                visible = true;
            }else{
                visible = false;
            };
            return visible;
        },
        isVisible: function(data) {

            let filter;
            if(myViewModel.stringValue() == ""){
                filter = true;
            }else if(data.content.includes(myViewModel.stringValue()) && myViewModel.stringValue() != ""){
                filter = true;
            }else{
                filter = false;
            };

            return filter;
        },
        message: ko.observableArray(obj),
        chats: ko.observableArray(comments),
        users: ko.observableArray(users),
        selectedUser: ko.observable(),

        // commentVisible: function(data1, data2) {
        //     let filter;
            
        //     console.log(data1,data2);
        //     console.log("========================================");
            
        //     if(data2.chat_id == data1.id){
        //         filter = true;
        //     }else{
        //         filter = false;
        //     };

        //     return filter;

        // },
        form1: ko.observable(""),
        form2: ko.observable(""),
        showEditForm: ko.observable(false),
        showCommentForm: ko.observable(false),
        showForm: ko.observable(true),
        stateBookmark: ko.observable('+ブックマークに追加')
    };

    myViewModel.showChannelSettings = function() {
            myViewModel.channelSettingsFormVisibility(!myViewModel.channelSettingsFormVisibility());
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

    // submit comment section
    myViewModel.submitComment = function() {
        event.preventDefault();
        // console.log(data);
        // console.log(message_id);
        let username = '<?php echo $loginUser; ?>';
        let content = document.getElementById("comment").value;
        let formData = {
            'chat_id': message_id,
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

        };

    // submit chat section

    myViewModel.submitMessage = function (){
        event.preventDefault();
        let username = '<?php echo $loginUser; ?>';
        let channelname = '<?php echo $channelname; ?>';
        let content = document.getElementById("content1").value;
        let formData = {
            'username': username,
            'content': content,
            'channelname': channelname,
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

    // delete chat section

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

    // edit chat section

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

    // post good section

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


    // bookmark section

    myViewModel.bookmark = function (msg){
        event.preventDefault();
        let message_id = msg['id'];
        // let bookmark = Math.abs(Number(msg['bookmark']) - 1);
        let formData = {
            'message_id': message_id,
            'cc_token': fuel_csrf_token()
        };
        console.log(formData);

        $.ajax({
            url: '<?php echo Uri::create('chat/bookmark.json'); ?>',
            type: 'POST',
            cache: false,
            dataType : 'json',
            data: formData,

        }).done(function(data) {
            // alert("成功");
            console.log("===========================================");
            console.log(data);

            // myViewModel.message(data);
            alert("ブックマークに登録しました。")

        }).fail(function() {
            alert("失敗");
        });
    };


    ko.applyBindings(myViewModel);


</script>


</html>



