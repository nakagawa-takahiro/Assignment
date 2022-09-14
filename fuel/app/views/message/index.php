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

<body id='container' onload="proc();">

<header style="color: white; background-color: #222222; top: 0; height: 3rem; padding-left: 1rem">
    <h1>CHANNEL NAME: <?php echo $channelname ?> Signed in as <?php echo $loginUser ?></h1>
    <nav style="display: inline-block">
        <a href="/channel/index">チャンネル一覧</a>
        <a href="/bookmark/index">ブックマーク一覧</a>
        <a href="" data-bind="click: showChannelSettings, text: channelSettings, visible: channelSettingsVisibility()"></a>
        <a href="/auth/logout">ログアウト</a>
        <a href="/profile/index/<?php echo $loginUser ?>">プロフィール</a>

    </nav>

    <form method="POST" action="" name="inviteUser" data-bind="visible: channelSettingsFormVisibility" style="color: black">
        チャンネル名を変更:
        <input type="text" id="newChannelname" placeholder="新しいチャンネル名を入力してください">
        <button data-bind="click: editChannelname">送信</button>
    </form>
    <form method="POST" action="" name="channelSettings" data-bind="visible: channelSettingsFormVisibility" style="color: black">
        チャンネルの公開範囲:<select name="number">
            <option value="1">public</option>
            <option value="2">private</option>
        </select>
        <button data-bind="click: editChannel">完了</button>
    </form>
    <form method="POST" action="" name="inviteUser" data-bind="visible: channelSettingsFormVisibility" style="color: black">
    ユーザーを招待する:
            <select data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'">
            </select>
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
            <span id="link" style="padding: 1rem; font-size: 20px" data-bind="text: username, value: username, click: $parent.moveToProf"></span> 
            <span data-bind="text: posted_at"></span><br>
            <div style="border: solid black 1px; padding: 1rem">
                <span style="white-space: pre-line;" data-bind="text: content, value: content"></span>
            </div>
            <span>👍</span><a href="#" style="padding-left: 5px" data-bind="click: $parent.postGood, text: res_good, value: res_good"></a>
            <span>👎</span><a href="#" style="padding-left: 5px" data-bind="click: $parent.postBad, text: res_bad, value: res_bad"></a>
            <a href="#" data-bind="click: $parent.editChat, text: $root.btn_edit($data)" style="padding-left: 1rem">編集</a>
            <a href="#" data-bind="click: $parent.deleteChat, text: $root.btn_delete($data)">削除</a>
            <a href="#" data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)" style="padding-left: 15px"></a><br>
            <br>
            <details id="detail">
            <summary data-bind="click: $parent.showComments" >スレッドを表示</summary>    
            <div data-bind="foreach: $parent.chats">
            <span data-bind="text: commented_by"></span><br>
            <span style="color: blue" data-bind="text: $root.mention_to($data)"></span>    <span data-bind="text: comment_content"></span><br><br>
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
        メンション:<select data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'"></select><br>
        <textarea type="text" id="comment" placeholder="コメントを入力してください"></textarea>
        <button data-bind="click: submitComment">送信</button>
    </form>

    </div>

    </main>
</body>

<script type="text/javascript">
    
    function proc() {
        if('<?php echo $current_message ?>' != "0"){
            let formData = {
                'username': '<?php echo $loginUser ?>',
                'channelname': '<?php echo $channelname ?>',
                'read_id': '<?php echo $current_message ?>',
                'cc_token': fuel_csrf_token()
            };

            $.ajax({
                url: '<?php echo Uri::create('chat/read_message.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                console.log("===========================================");
                console.log(data);

            }).fail(function() {
                alert("失敗");
            });
        };
    };


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
    
    let comments = 
        <?php
        $json=json_encode($comment,JSON_PRETTY_PRINT);
        echo $json;
        ?>;
    
    let bookmarks = 
        <?php
        $json=json_encode($bookmarktext,JSON_PRETTY_PRINT);
        echo $json;
        ?>;

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

    // setInterval(dataCheck, 5000);

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
        bookmarks: ko.observableArray(bookmarks),
        selectedUser: ko.observable(),
        form1: ko.observable(""),
        form2: ko.observable(""),
        showEditForm: ko.observable(false),
        showCommentForm: ko.observable(false),
        showForm: ko.observable(true),
        stateBookmark: function(state) {
            console.log(state);
            if(bookmarks.indexOf(state.id) != -1) {
                return "-ブックマークから削除";
            }else{
                return "+ブックマークに追加";
            }
        },
        
        mention_to: function(isOpen) {
            // console.log(isOpen);
                let mention;
                if( isOpen.mention_to != "all" ) {
                    mention = '@' + isOpen.mention_to;
                }else{
                    mention = "";
                };
                return mention;
        },

        btn_edit: function(editdata) {
                let text;
                if( editdata.username == '<?php echo $loginUser; ?>' ) {
                    text = '編集';
                }else{
                    text = "";
                };
                return text;
        },

        btn_delete: function(deletedata) {
                let text;
                if( deletedata.username == '<?php echo $loginUser; ?>' ) {
                    text = '削除';
                }else{
                    text = "";
                };
                return text;
        },
    };

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
            url: '<?php echo Uri::create('register/edit_channelvisibility.json'); ?>',
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

        };

    // submit chat section

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

    myViewModel.moveToProf = function(user) {
        console.log(user);
            let link = document.getElementById('link');
            let url = '<?php echo Uri::create('profile/index/'); ?>'+user['username'];
            link.setAttribute('href', url);
            window.location.href = url;
    };


    ko.applyBindings(myViewModel);


</script>


</html>