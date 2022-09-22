<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.0/knockout-min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.8.3.js" integrity="sha256-dW19+sSjW7V1Q/Z3KD1saC6NcE5TUIhLJzJbrdKzxKc=" crossorigin="anonymous"></script>
    <!-- <script type='text/javascript' src='knockout-3.5.1.js'></script> -->
    <?php echo \Security::js_fetch_token(); ?>
    <?php echo Asset::js('SplitView.js'); ?>
    <?php echo Asset::js('knockout-3.5.1.js'); ?>
    <?php echo Asset::css('splitview.css'); ?>
    <?php echo Asset::css('style.css'); ?>
</head>

<body class="contents_box" id="container">
<div id="loader" class="show" data-bind="visible: showLoad"></div>
<div class="overlay show" data-bind="visible: showLoad"></div>

    <header style="top: 0">
        <div id="header" >
            <h1 class="imsg-head" style="color: white">課題アプリ</h1>
            <h1 class="imsg-head" style="color: white; margin-left: 1rem" data-bind="text: channelname"></h1>
            <h1 class="imsg-head imsg-head-date" style="color: white"><?php echo "Signed in as $loginUser"; ?></h1>   
        </div>
         
        <div id="navi">
            <nav>
                <!-- <a href="/channel/index">チャンネル一覧</a> -->
                <a href="" data-bind="click: showBookmark">ブックマーク一覧</a>
                <a href="" data-bind="click: showChannelSettings, text: channelSettings, visible: channelSettingsVisibility()" class="js-modal-open" data-target="modal01"></a>
                <a href="" data-bind="click: showProfile, text: profileText"></a>
                <a class="js-modal-open" data-target="modal03">ユーザー一覧</a>
                <a href="/auth/logout">ログアウト</a>
                <form class="imsg-head imsg-head-date">
                    <span style="min-width: max-content;">フィルター🔍</span>
                    <input style="min-width: -webkit-fill-available; margin-right: 5rem" type="text" data-bind='value: stringValue, valueUpdate: "afterkeydown"' placeholder="メッセージを入力してください">
                </form>
            </nav>
        </div>
    </header>


    <div id="modal01" class="modal js-modal form-block">
        <div class="modal-header">
            チャンネル設定
        </div>
        <div class="modal__content form-block-body">
            <div>
                <form method="POST" action="" name="inviteUser">
                    <span class="input-label">チャンネル名を変更</span>
                    <input type="text" id="newChannelname" placeholder="新しいチャンネル名を入力してください">
                    <button data-bind="click: editChannelname">送信</button>
                </form>
                <form method="POST" action="" name="channelSettings">
                    <span class="input-label" style="padding-top: 2rem">チャンネルの公開範囲</span>
                    <select name="number">
                        <option value="1">public</option>
                        <option value="2">private</option>
                    </select>
                    <button data-bind="click: editChannel">完了</button>
                </form>
                <form method="POST" action="" name="inviteUser">
                    <span class="input-label" style="padding-top: 2rem">ユーザーを招待する</span>
                    <select data-bind="options: users, value: selectedUser, optionsCaption: '-選択してください-'"></select>
                    <button data-bind="click: inviteUser">送信</button>
                </form>
            </div>
            <div class="close flex">
                <a class="js-modal-close  btn close_info_modal" href="">閉じる</a>
            </div>

        </div>
    </div>

    <div id="modal02" class="modal js-modal form-block">
        <div class="form-block-header">
            チャンネル作成
        </div>
        <div class="modal__content form-block-body">
            <form method="POST" action="" name="channel">
            <span class="input-label">チャンネル名</span> 
            <input type="text" id="addChannel" name='channel' placeholder="登録するチャンネル名を入力してください。">
            <span class="input-label" style="padding-top: 2rem">チャンネルの公開範囲</span> 
                <select name="number">
                    <option value="1">public</option>
                    <option value="2">private</option>
                </select><br>
                <div class="btn-container-btm btn-container-centered">
                    <button class="btn btn-green btn-large btn_update_msetting" data-bind="click: addChannel">送信</button>
                </div>
            </form>
            <div class="close flex">
                <a class="js-modal-close btn close_info_modal" href="">閉じる</a>
            </div>
        </div>
    </div>

    <div id="modal03" class="modal js-modal form-block">
        <div class="form-block-header">
            このチャンネルにいるユーザー
        </div>
        <div class="modal__content form-block-body">
            <table class="table-plain table">
                <thead>
                    <tr><th>ユーザー名</th></tr>
                </thead>
                <tbody data-bind="foreach: userlist">
                    <tr class="bold">
                        <td data-bind="text: $parent.username_ownerchecked($data), click: $parent.moveToUserProfile" style="cursor: pointer;"></td>
                    </tr>
                </tbody>
            </table>

            <div class="close flex">
                <a class="js-modal-close btn close_info_modal" href="">閉じる</a>
            </div>
        </div>
    </div>

    <main id="main" class="split-view horizontal" style="padding: 1rem; top: 0; background-color: #fff">
        
    <div class="contents_box1 split-view vertical" style="position: fixed; bottom: 0; top: 5rem; border-right: solid #eee 5px; padding: 1rem; " >
        <div class="contents_box">
            <h1 class="form-block-header">チャンネル一覧</h1>

            <div data-bind="foreach: channels">
                <div data-bind="visible: $parent.channelVisible($data)">
                    <span data-bind="text: $parent.keyIcon($data)"></span>
                    <a class="sidebar-accordion-body" id="link" href="#" data-bind="click: $parent.moveToChannel, text: channelname, value: channelname, style: { backgroundColor: $parent.bgcolor($data), color: $parent.textcolor($data), fontSize: $parent.fontsize($data) }"></a>
                    <span data-bind="text: $parent.readOrNot($data)" style="color: white; background-color: #e11b74; border-radius: 10px;"></span>
                </div>
                
            </div>

            <div class="js-modal-open" href="" data-target="modal02">
                <p data-bind="click: showAddChannelForm" style="cursor: pointer;">チャンネルを追加</p>
            </div>
            <form class="imsg-head imsg-head-date">
                <input style="min-width: -webkit-fill-available;" type="text" data-bind='value: channelStringValue, valueUpdate: "afterkeydown"' placeholder="チャンネルを検索できます。">
            </form>
        </div>
        <div class="gutter"></div>

        <div class="contents_box">
            <h1 class="form-block-header">通知一覧</h1>

            <table class="table-plain table">
                <thead>
                    <tr><th>内容</th></tr>
                </thead>
                <tbody data-bind="foreach: notification">
                    <tr class="bold" data-bind="click: $parent.removeNotification" style="cursor: pointer;">
                        <td>
                            <span data-bind="text: username_from" style="color: navy"></span>
                            <span> invited you to </span>
                            <span data-bind="text: channelname" style="color: crimson"></span>
                        </td>
                    </tr>
                </tbody>

                <tbody data-bind="foreach: mention">
                    <tr class="bold" data-bind="click: $parent.removeMention" style="cursor: pointer;">
                        <td>
                            <span>Mentioned by </span>
                            <span data-bind="text: commented_by" style="color: navy"></span>
                            <span> in </span>
                            <span data-bind="text: channelname" style="color: crimson"></span>
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>
    

    <div class="contents_box2" style="margin-left: 21%; margin-bottom: 6rem; overflow-y: scroll; height: 100vh; background-color: #fff">

        <div data-bind="visible: isVisible()" style="padding-top: 2rem">

            <div style="position: fixed; top: 50px; color: black;">
                <div style="margin-top: 2rem;">

                </div>
                
            </div>
            <div style="margin: 0rem 2rem 0rem 2rem;">
            
            <h1 class="form-block-header" style="margin-top: 3rem; ">メッセージ <span data-bind="text: message_intro_text"></span></h1>

                <div id="message" data-bind="foreach: messages">
            
                
                <div class="intro-msg chat_mycompany" data-bind="visible: $parent.messageVisible($data), style: { backgroundColor: $parent.color($data) }">
                    <div class="imsg-head">
                        <span style="padding: 1rem; cursor: pointer; font-size: 20px" data-bind="text: username, value: username, click: $parent.moveToUserProfile"></span> 
                        <span class="imsg-head-date" data-bind="text: posted_at"></span><br>
                    </div>    

                    <div style="border: solid white 1px; padding: 1rem; background-color: white; color: black;">
                        <span style="white-space: pre-line;" data-bind="text: content, value: content"></span>
                    </div>
                    <div style="padding: 1rem; ">
                        <span>👍</span><a href="#" style="text-decoration: none; " data-bind="click: $parent.postGood, text: res_good, value: res_good"></a>
                        <span>👎</span><a href="#" style="padding-left: 5px; text-decoration: none;" data-bind="click: $parent.postBad, text: res_bad, value: res_bad"></a>
                        <a href="#" data-bind="click: $parent.editChat, text: $root.btn_edit($data)" style="padding-left: 1rem; text-decoration: none;">編集</a>
                        <a href="#" data-bind="click: $parent.deleteChat, text: $root.btn_delete($data)" style="text-decoration: none;">削除</a>
                        <a href="#" data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)" style="padding-left: 15px; text-decoration: none; "></a>
                    </div>
                    
                    <div>
                        <a href="" data-bind="click: $parent.showComments" style="text-decoration: none;">スレッドを表示</a>
                    </div>
                </div>
                </div>
                
                
            </div>
            <div style="position: fixed; bottom: 0px; width: -webkit-fill-available;">
                <div style="background-color: white; padding: .5rem;">
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
                        <textarea type="text" id="comment" data-bind='value: form3' placeholder="コメントを入力してください"></textarea>
                        <button data-bind="click: submitComment">送信</button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    

    <div class="gutter"></div>


    <div class="contents_box2" data-bind="visible: bookmarkVisibility" style="overflow-y: scroll; height: 100vh;">
        <div style="margin: 3rem; margin-top: 5rem; margin-bottom: 6rem">
            
            <div class="form-block-header imsg-head">
                <h1 class="imsg-head">ブックマーク</h1>
                <span class="imsg-head-date" data-bind="click: closeBookmark" style="font-size: 1.5rem; cursor: pointer;">×</span>
            </div>

            <div data-bind="foreach: bookmarks">
                <div class="intro-msg chat_mycompany" data-bind="style: { backgroundColor: $parent.color($data) }">
                    <div style="border-bottom: solid white 1px">
                        <span data-bind="text: channelname, value: channelname"></span>
                    </div>
                    <div class="imsg-head">
                        <span style="padding: 1rem; font-size: 20px" data-bind="text: username, value: username"></span> 
                        <span class="imsg-head-date" data-bind="text: posted_at"></span><br>
                    </div>    

                    <div style="border: solid white 1px; padding: 1rem; background-color: white; color: black;">
                        <span style="white-space: pre-line;" data-bind="text: content, value: content"></span>
                    </div>
                    <div style="padding: 1rem; ">
                        <span>👍</span><a href="#" style="text-decoration: none; " data-bind="click: $parent.postGood, text: res_good, value: res_good"></a>
                        <span>👎</span><a href="#" style="padding-left: 5px; text-decoration: none;" data-bind="click: $parent.postBad, text: res_bad, value: res_bad"></a>
                        <a href="" data-bind="click: $parent.bookmark">-ブックマークから削除</a>
                    </div>
                    <div>
                        <a href="" data-bind="click: $parent.moveToChannel" style="text-decoration: none;">このメッセージへ移動</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="contents_box2" data-bind="visible: commentsVisibility" style="overflow-y: scroll; height: 100vh;">
        <div style="margin: 3rem; margin-top: 5rem; margin-bottom: 6rem" >
            
            <div class="form-block-header imsg-head">
                <h1 class="imsg-head">スレッド</h1>
                <span class="imsg-head-date" data-bind="click: closeComments" style="font-size: 1.5rem; cursor: pointer;">×</span>
            </div>

            <div data-bind="foreach: messageforcomment">
                <div class="intro-msg chat_mycompany" data-bind="style: { backgroundColor: $parent.color($data) }">
                    <div class="imsg-head" >
                        <span style="padding: 1rem; font-size: 20px" data-bind="text: username, value: username"></span> 
                        <span class="imsg-head-date" data-bind="text: posted_at"></span><br>
                    </div>    

                    <div style="border: solid white 1px; padding: 1rem; background-color: white; color: black;">
                        <span style="white-space: pre-line;" data-bind="text: content, value: content"></span>
                    </div>
                    <div style="padding: 1rem; ">
                        <span>👍</span><a href="#" style="text-decoration: none; " data-bind="click: $parent.postGood, text: res_good, value: res_good"></a>
                        <span>👎</span><a href="#" style="padding-left: 5px; text-decoration: none;" data-bind="click: $parent.postBad, text: res_bad, value: res_bad"></a>
                        <a href="#" data-bind="click: $parent.editChat, text: $root.btn_edit($data)" style="padding-left: 1rem; text-decoration: none;">編集</a>
                        <a href="#" data-bind="click: $parent.deleteChat, text: $root.btn_delete($data)" style="text-decoration: none;">削除</a>
                        <a href="#" data-bind="click: $parent.bookmark, text: $parent.stateBookmark($data)" style="padding-left: 15px; text-decoration: none; "></a>
                    </div>
                </div>
                
            </div>
            <div data-bind="foreach: chats" >
                <div class="intro-msg chat_intcompany">
                    
                    <div class="imsg-head">
                        <span style="padding: 1rem; font-size: 20px" data-bind="text: commented_by, value: commented_by"></span> 
                        <span class="imsg-head-date" data-bind="text: posted_at"></span><br>
                    </div>    

                    
                    <div style="padding: 1rem; background-color: white; color: black;">
                        <p style="color: blue" data-bind="text: $root.mention_to($data)"></p>
                        <span data-bind="text: comment_content, value: comment_content"></span>
                    </div>
                </div>
            </div>
            <a href="" data-bind="click: comment">コメントを追加する</a>
        </div>    

    </div>

    <div class="contents_box2 list-item-body" data-bind="visible: profileVisibility" style="overflow-y: scroll; height: 100vh;">
        <div class="list-item-body-left" style="margin: 3rem; margin-top: 5rem; margin-bottom: 6rem">
        
            <div class="form-block-header imsg-head">
                <h1 class="imsg-head">プロフィール</h1>
                <span class="imsg-head-date" data-bind="click: closeProfile" style="font-size: 1.5rem; cursor: pointer;">×</span>
            </div>
            
            <p class="student-name" data-bind="text: username"></p>
            <p class="introduction-student-area" data-bind="text: self_introduction"></p>
			<a data-bind="text: url_link, attr: {href: url_link}"></a>
            <div>
                <a href="" data-bind="click: moveToDM, visible: dmVisible">DMはこちらのリンクから</a>
                <a href="" data-bind="click: editProf, visible: profVisible">プロフィールを編集する</a>
            
                <form action="" method="post" data-bind="visible: showProfForm"  >
                    <div class="input-label" style="padding-top: 1rem">
                        自己紹介文
                    </div>
                    <textarea type="text" id="content" placeholder="ここに文章を入力してください" data-bind="text: editContent"></textarea>
                    <div class="input-label" style="padding-top: 2rem">
                        URL
                    </div>
                    <input type="text" id="url_link" placeholder="URL" data-bind="text: editUrl">
                    <div class="btn-container-btm btn-container-centered">
                        <button class="btn btn-green btn-large btn_update_msetting" data-bind="click: submitNewProf">完了</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    </main>

    
    <script>
        SplitView.activate(document.getElementById("main"))
    </script>

    <script type="text/javascript">

        $(function(){
            $('.js-modal-open').each(function(){
                $(this).on('click',function(){
                    var target = $(this).data('target');
                    var modal = document.getElementById(target);
                    $(modal).fadeIn();
                    return false;
                });
            });
            $('.js-modal-close').on('click',function(){
                $('.js-modal').fadeOut();
                return false;
            }); 
        });

        let channelname;
        let user_selected;
        let userlist = [];
        let current_message = "0";
        let message_id;
        let channelData = {id: "0", owner: "null"};

        let obj = 
            <?php
            $json=json_encode($profdata,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        // console.log(obj);


        let data = 
            <?php
            $json=json_encode($data,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        console.log(data);

        let notification = 
            <?php
            $json=json_encode($notification,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        // console.log(notification);

        let mention = 
            <?php
            $json=json_encode($mention,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        // console.log(mention);

        
        let bookmarks = 
            <?php
            $json=json_encode($bookdata,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        // console.log(bookmarks);

        let usersObj = 
            <?php
            $json=json_encode($users,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
            // console.log(usersObj);
        let users = usersObj.map(function(item) {
            return item.username
        });

        let comments = 
            <?php
            $json=json_encode($comment,JSON_PRETTY_PRINT);
            echo $json;
        ?>;
        // console.log(comments);

        function getIndex(value, arr, prop) {
            for(let i = 0; i < arr.length; i++) {
                if(arr[i][prop] === value) {
                    return i;
                }
            }
            return -1; //値が存在しなかったとき
        };

        let message_data = [];

        let myViewModel = {
            stringValue: ko.observable(""),
            channelStringValue: ko.observable(""),
            channels: ko.observableArray(data),
            userlist: ko.observableArray(userlist),
            notification: ko.observableArray(notification),
            mention: ko.observableArray(mention),
            messages: ko.observableArray(message_data),
            messageforcomment: ko.observableArray(""),
            addChannelForm: ko.observable(false),
            bookmarkVisibility: ko.observable(false),
            commentsVisibility: ko.observable(false),
            form1: ko.observable(""),
            form2: ko.observable(""),
            form3: ko.observable(""),
            showForm: ko.observable(false),
            showEditForm: ko.observable(false),
            showCommentForm: ko.observable(false),
            selectedUser: ko.observable(),
            bookmarks: ko.observableArray(bookmarks),
            chats: ko.observableArray(comments),
            channelname: ko.observable(),
            message_id: ko.observable(),
            message_intro_text: ko.observable("※チャンネルを選択して下さい"),
            channelSettings: ko.observable("チャンネル設定"),
            channelSettingsFormVisibility: ko.observable(false),
            channelSettingsVisibility: ko.observable(false),
            username: ko.observable(obj.username),
            self_introduction: ko.observable(obj.self_introduction),
            url_link: ko.observable(obj.url_link),
            showProfForm: ko.observable(false),
            editContent: ko.observable(obj.self_introduction),
            editUrl: ko.observable(obj.url_link),
            profileText: ko.observable("プロフィール"),
            profileVisibility: ko.observable(false),
            profileData: ko.observable(obj),
            profVisible: ko.observable(false),
            dmVisible: ko.observable(false),
            showLoad: ko.observable(false),
            

            color: function(message) {
                if(message.username == '<?php echo $loginUser; ?>'){
                    return "#3f4e66";
                }else{
                    return "#6f89b4";
                }

            },

            bgcolor: function(channel) {
                if(channel.channelname == myViewModel.channelname()){
                    return "#6f89b4";
                }else{
                    return "transparent";
                }

            },

            textcolor: function(channel) {
                if(channel.channelname == myViewModel.channelname()){
                    return "#fff";
                }
            },

            fontsize: function(channel) {
                if(channel.channelname == myViewModel.channelname()){
                    return "1.1rem";
                }
            },

            keyIcon: function(isOpen) {
                console.log(isOpen);
                let locked;
                if( isOpen.owner == "dm") {
                    locked = "👥";
                }else if( isOpen.open == "1" && isOpen.private == "1" ) {
                    locked = "👤";
                }
                else if( isOpen.open == "1" ) {
                    locked = "🔒";
                }else{
                    locked = "📖";
                };
                return locked;
            },

            username_ownerchecked: function(owner) {
                let username;
                console.log(owner);
                // console.log(channelData);
                if( owner.username == channelData.owner) {
                    username = owner.username + "👑";
                }else{
                    username = owner.username;
                };
                return username;
            },

            readOrNot: function(value) {
                console.log(value);
                let read;
                if( value.unread_count == "0" ) {
                    read = "";
                }else{
                    read = "+" + value.unread_count;
                };
                return read;
            },

            stateBookmark: function(state) {

                if(bookmarks.find(e => e.id === state.id)) {
                    return "-ブックマークから削除";
                }
                else{
                    return "+ブックマークに追加";
                }
            },
            

            messageVisible: function(data) {

                let filter;
                if(myViewModel.stringValue() == ""){
                    filter = true;
                }else if(data.content.includes(myViewModel.stringValue()) && myViewModel.stringValue() !== ""){
                    filter = true;
                }else{
                    filter = false;
                };

                return filter;
            },

            channelVisible: function(data) {

                let filter;
                if(myViewModel.channelStringValue() == ""){
                    filter = true;
                }else if(data.channelname.includes(myViewModel.channelStringValue()) && myViewModel.channelStringValue() !== ""){
                    filter = true;
                }else{
                    filter = false;
                };

                return filter;
            },

            isVisible: function() {

                let filter;
                if(message_data == ""){
                    filter = true;
                }else{
                    filter = false;
                };

                return filter;
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

            mention_to: function(isOpen) {
                // console.log(isOpen);
                let mention;
                if( isOpen.mention_to !== "all" ) {
                    mention = '@' + isOpen.mention_to;
                }else{
                    mention = "";
                };
                return mention;
            },
            
        };

        myViewModel.showProfile = function() {

            event.preventDefault();
            let profile_user = '<?php echo $loginUser ?>';

            let formData = {
                'profile_user': profile_user,
                'cc_token': fuel_csrf_token()
            };

            console.log(formData);

            $.ajax({
                url: '<?php echo Uri::create('messages/get_profile.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                console.log("===========================================");
                console.log(data);
                // myViewModel.profileData(myViewModel.profileData(data));
                myViewModel.profVisible(true);
                myViewModel.dmVisible(false);
                myViewModel.username(data.username);
                myViewModel.self_introduction(data.self_introduction);
                myViewModel.url_link(data.url_link);
                if(myViewModel.profileVisibility()==false){
                    myViewModel.profileVisibility(!myViewModel.profileVisibility());
                    myViewModel.bookmarkVisibility(false);
                    myViewModel.commentsVisibility(false);
                }

            }).fail(function() {
                alert("失敗");
            });            
            
        };

        myViewModel.closeBookmark = function() {
            myViewModel.bookmarkVisibility(false);
        };

        myViewModel.closeProfile = function() {
            myViewModel.profileVisibility(false);
        };

        myViewModel.closeComments = function() {
            myViewModel.commentsVisibility(false);
        };

        myViewModel.moveToUserProfile = function(user) {

            event.preventDefault();
            myViewModel.profileVisibility(false);

            let profile_user = user.username;

            let formData = {
                'profile_user': profile_user,
                'cc_token': fuel_csrf_token()
            };

            console.log(formData);

            $.ajax({
                url: '<?php echo Uri::create('messages/get_profile.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                console.log("===========================================");
                console.log(data);
                // myViewModel.profileData(myViewModel.profileData(data));

                if(data.username == '<?php echo $loginUser ?>'){
                    myViewModel.dmVisible(false);
                    myViewModel.profVisible(true);
                }else{
                    myViewModel.dmVisible(true);
                    myViewModel.profVisible(false);                    
                }

                myViewModel.username(data.username);
                myViewModel.self_introduction(data.self_introduction);
                myViewModel.url_link(data.url_link);
                if(myViewModel.profileVisibility()==false){
                    myViewModel.profileVisibility(!myViewModel.profileVisibility());
                    myViewModel.bookmarkVisibility(false);
                    myViewModel.commentsVisibility(false);
                };
                user_selected = profile_user;


            }).fail(function() {
                alert("失敗");
            });

        };

        myViewModel.moveToDM = function() {
            event.preventDefault();
            // console.log(user_selected);
            let channelname = user_selected + '-' + '<?php echo $loginUser ?>';
            let profile_user = user_selected;
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
                myViewModel.messages(data['message_data']);
                myViewModel.message_intro_text("In " + data['channelname'].channelname);
                myViewModel.channelname(data['channelname'].channelname);

            }).fail(function() {
                alert("失敗");
            });
        };

        myViewModel.editProf = function() {

            event.preventDefault();
            myViewModel.showProfForm(!myViewModel.showProfForm());

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
                myViewModel.url_link(data.url_link);
                myViewModel.self_introduction(myViewModel.self_introduction());
                myViewModel.url_link(myViewModel.url_link());

                myViewModel.showProfForm(!myViewModel.showProfForm());


            }).fail(function() {
                alert("失敗");
            });


        }


        myViewModel.showChannelSettings = function() {
            myViewModel.channelSettingsFormVisibility(!myViewModel.channelSettingsFormVisibility());
        };
        
        myViewModel.editChannelname = function() {
            event.preventDefault();
            let newChannelname = document.getElementById("newChannelname").value;

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
                // alert("新しいチャンネルに移動します。");
                console.log("===========================================");
                console.log(data);
                myViewModel.channels(data['newchanneldata']);
                myViewModel.channelname(data['newchannelname']);
                myViewModel.message_intro_text("In " + data['newchannelname']);

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
                myViewModel.channels(data);


            }).fail(function() {
                alert("失敗");
            });

            };

        myViewModel.inviteUser = function() {

            event.preventDefault();

            let invite_user = myViewModel.selectedUser();
            let username = '<?php echo $loginUser; ?>';
            let content = username + 'が' + invite_user + 'を招待しました。';
            let each_channel_id = current_message;

            let formData = {
                'username': username,
                'each_channel_id': Number(each_channel_id) + 1,
                'content': content,
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
                myViewModel.messages.push(data);
                myViewModel.messages(myViewModel.messages());
                // myViewModel.channels(data);

            }).fail(function() {
                alert("失敗");
            });

        };

        myViewModel.showAddChannelForm = function() {
            myViewModel.addChannelForm(!myViewModel.addChannelForm());
        };

        myViewModel.showBookmark = function() {
            event.preventDefault();
            
            myViewModel.bookmarkVisibility(!myViewModel.bookmarkVisibility());
            myViewModel.commentsVisibility(false);
        };


        function proc() {
            if(current_message !== "0"){
                let formData = {
                    'username': '<?php echo $loginUser ?>',
                    'channelname': channelname,
                    'read_id': current_message,
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

        myViewModel.moveToChannel = function(channel) {
            
            event.preventDefault();
            myViewModel.showLoad(true);


            let formData = {
                'channelname': channel['channelname'],
                'cc_token': fuel_csrf_token()
            };
            console.log(formData);

            $.ajax({
                url: '<?php echo Uri::create('messages/get_message.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                console.log("===========================================");
                console.log(data);
                message_data = data['data'];
                userlist = data['users'];
                channelname = channel['channelname'];
                current_message = data['current_message'];
                channelData = data['channelData'];
                current_message = data['current_message'];
                newchanneldata = data['channeldata'];
                console.log(userlist);

                myViewModel.messages(message_data);
                myViewModel.userlist(userlist);
                myViewModel.channelname(channelname);
                myViewModel.channels(newchanneldata);
                myViewModel.message_intro_text("In " + channelname);

                if (channelData.owner == '<?php echo $loginUser; ?>'){
                    myViewModel.channelSettingsVisibility(true);
                }else{
                    myViewModel.channelSettingsVisibility(false);
                }
                myViewModel.channelSettingsVisibility(myViewModel.channelSettingsVisibility());
                myViewModel.messages(myViewModel.messages());
                myViewModel.showForm(true);
                proc();

                var obj = document.getElementById('message');
                obj.scrollIntoView(false);


            }).fail(function() {
                alert("失敗");
            }).always(function() {
                myViewModel.showLoad(false);

            });
        };

        myViewModel.removeNotification = function(channel) {

            let channelname = channel.channelname;
            let username_to = channel.username_to;
            let username_from = channel.username_from;

            let formData = {
                'channelname': channelname,
                'username_to': username_to,
                'username_from': username_from,
                'cc_token': fuel_csrf_token()
            };
            console.log(formData);
            $.ajax({
                url: '<?php echo Uri::create('register/invite_checked.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                alert("成功");
                console.log("===========================================");
                console.log(data);
                // channelname = data['channelname'];
                myViewModel.message_intro_text("In " + channelname);
                myViewModel.channelname(channelname);
                // myViewModel.bgcolor();

                myViewModel.notification(data['invite']);
                myViewModel.messages(data['message_data']);

            }).fail(function() {
                alert("失敗");
            });

        };

        myViewModel.removeMention = function(mention) {
            console.log(mention);

            let chat_id = mention.id;
            let message_id = mention.chat_id;
            let username = mention.commented_by;

            let formData = {
                'chat_id': chat_id,
                'message_id': message_id,
                'username': username,
                'cc_token': fuel_csrf_token()
            };
            console.log(formData);

            $.ajax({
                url: '<?php echo Uri::create('register/mention_checked.json'); ?>',
                type: 'POST',
                cache: false,
                dataType : 'json',
                data: formData,

            }).done(function(data) {
                alert("成功");
                console.log("===========================================");
                console.log(data);

                if(myViewModel.commentsVisibility() == false){
                    myViewModel.commentsVisibility(!myViewModel.commentsVisibility());
                };

                if(myViewModel.bookmarkVisibility() == true){
                    myViewModel.bookmarkVisibility(!myViewModel.bookmarkVisibility());
                };

                myViewModel.chats(data['comment_data']);
                myViewModel.chats(myViewModel.chats());
                channelname = data['message_data'].channelname;
                message_id = data['message_data'].id;
                myViewModel.messageforcomment(data['message_data']);
                
                myViewModel.messageforcomment(myViewModel.messageforcomment());

                myViewModel.mention(data['mention']);

                console.log(message_id);
                console.log(channelname);

            }).fail(function() {
                alert("失敗");
            });

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


        myViewModel.submitMessage = function (){
            event.preventDefault();
            let username = '<?php echo $loginUser; ?>';
            let content = document.getElementById("content1").value;
            let each_channel_id = current_message;
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

                myViewModel.messages.push(data);
                myViewModel.messages(myViewModel.messages());
                myViewModel.form1("");
                var obj = document.getElementById('message');
                obj.scrollIntoView(false);

            }).fail(function() {
                alert("失敗");
            });
        };

        // post good section

        myViewModel.postGood = function(msg) {
            event.preventDefault();
            let goodId = msg['id'];
            let goodCount = Number(msg['res_good']) + 1;
            

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
                let index = getIndex(goodId, message_data, 'id');
                myViewModel.messages()[index] = data;
                myViewModel.messages(myViewModel.messages());


            }).fail(function() {
                alert("失敗");
            });

        }

        // post bad section

        myViewModel.postBad = function(msg) {
            event.preventDefault();
            let badId = msg['id'];
            let badCount = Number(msg['res_bad']) + 1;
            

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
                let index = getIndex(badId, message_data, 'id');
                myViewModel.messages()[index] = data;
                myViewModel.messages(myViewModel.messages());

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
            console.log(bookmarks);
            console.log(msg);
            if(bookmarks.find(e => e.id === msg.id)){
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
                'channelname': channelname,
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

                // var result = bookmarks.filter((value) => {
                //     return (JSON.stringify(value) !=== JSON.stringify(data['bookmark']));
                //     console.log(value.id);
                
                // });

                if(bookmark == "add"){
                    myViewModel.bookmarks.push(data['bookmark']);
                }

                if(bookmark == "delete"){
                    myViewModel.bookmarks(data['bookmark']);

                }
                myViewModel.messages(data['message_data']);
                myViewModel.messages(myViewModel.messages());

                // alert(alertmsg);

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

                    let index = getIndex(editChatId, message_data, 'id');
                    myViewModel.messages()[index] = data;
                    myViewModel.messages(myViewModel.messages());

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

        // delete chat section

        myViewModel.deleteChat = function(msg) {

            event.preventDefault();
            let id = msg['id'];
            
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

                let index = getIndex(id, message_data, 'id');
                myViewModel.messages.remove(myViewModel.messages()[index]);
                myViewModel.messages(myViewModel.messages());

                // myViewModel.message(data);
                alert("メッセージを削除しました。")

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
                // console.log(myViewModel.chats());

                myViewModel.chats(data);
                myViewModel.form3("");
                
                myViewModel.chats(myViewModel.chats());
                myViewModel.showCommentForm(false);
                myViewModel.showEditForm(false);
                myViewModel.showForm(true);
                var obj = document.getElementById('message');
                obj.scrollIntoView(false);

            }).fail(function() {
                alert("失敗");
            });

        };

        // show comments section
        myViewModel.showComments = function(details) {
            event.preventDefault();
            myViewModel.showLoad(true);
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

                if(myViewModel.commentsVisibility() == false){
                    myViewModel.commentsVisibility(!myViewModel.commentsVisibility());
                };

                if(myViewModel.bookmarkVisibility() == true){
                    myViewModel.bookmarkVisibility(!myViewModel.bookmarkVisibility());
                };

                myViewModel.chats(data);
                myViewModel.chats(myViewModel.chats());
                myViewModel.messageforcomment(details);
                myViewModel.messageforcomment(myViewModel.messageforcomment());
                
                message_id = details.id;
                console.log(message_id);

            }).fail(function() {
                alert("失敗");
            }).always(function() {
                myViewModel.showLoad(false);
            });
            return true;
            
        }

        myViewModel.comment = function(msg) {
            // console.log(msg);
            // message_id = msg['id'];

            event.preventDefault();
            myViewModel.showCommentForm(true);
            myViewModel.showEditForm(false);
            myViewModel.showForm(false);

        };

        ko.applyBindings(myViewModel);

    </script>
</body>
</html>
