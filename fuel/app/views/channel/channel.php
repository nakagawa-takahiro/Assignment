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
<body>

    <header style="color: white; background-color: #222222; top: 0; height: 3rem; padding-left: 1rem">
    <h1><?php echo "Signed in as $loginUser"; ?></h1>    
    <nav style="display: inline-block">
            <a href="/channel/index">チャンネル一覧</a>
            <a href="/bookmark/index">ブックマーク一覧</a>
            <a href="/auth/logout">ログアウト</a>
        </nav>

    </header>
    <main style="padding: 1rem; margin-top: 2.5rem">
        <h1>チャンネル一覧</h1>

        <div data-bind="foreach: channels">
            <span data-bind="text: $parent.keyIcon($data)"></span>
            <a id="link" href="#" data-bind="click: $parent.moveToChannel, text: channelname, value: channelname"></a>
            
            <span data-bind="text: $parent.readOrNot($data)" style="color: red"></span>
            <br>
        </div>

        <br>
        <div>
            <p data-bind="click: showAddChannelForm">チャンネルを追加</p>
            <form method="POST" action="" name="channel" data-bind="visible: addChannelForm">
                チャンネル名:<input type="text" id="addChannel" name='channel' placeholder="登録するチャンネル名を入力してください。">
                チャンネルの公開範囲：<select name="number">
                    <option value="1">public</option>
                    <option value="2">private</option>
                </select><br>
                <button data-bind="click: addChannel">送信</button>
            </form>
        </div>
        <br>
        <p>お知らせ一覧</p>
        <div data-bind="foreach: notification">
        <div style="border: solid black 1px">
            FROM: <span data-bind="text: commented_by"></span><br>
            <span data-bind="text: comment_content"></span><br>
            <a href="" id="link2" data-bind="click: $parent.moveToChannelViaNotification">チャンネルへ移動</a>
        </div>

        </div>


    </main>

    <script type="text/javascript">

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

        let myViewModel = {
            channels: ko.observableArray(data),
            addChannelForm: ko.observable(false),
            keyIcon: function(isOpen) {
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
            readOrNot: function(value) {
                // console.log(value);
                let read;
                if( value.unread_count == "0" ) {
                    read = "";
                }else{
                    read = '+' + value.unread_count;
                };
                return read;
            }
        };

        myViewModel.showAddChannelForm = function() {
            myViewModel.addChannelForm(!myViewModel.addChannelForm());
        };

        myViewModel.moveToChannel = function(channel) {
            let link = document.getElementById('link');
            let url = '<?php echo Uri::create('message/index/'); ?>'+channel['channelname'];
            link.setAttribute('href', url);
            window.location.href = url;
        };

        myViewModel.moveToChannelViaNotification = function(channel) {
            // event.preventDefault();
            // console.log(channel.channelname);
            let link = document.getElementById('link2');
            let id = channel.channelname;
            let url = '<?php echo Uri::create('message/index/'); ?>'+channel.channelname;
            link.setAttribute('href', url);
            window.location.href = url;

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




        ko.applyBindings(myViewModel);

    </script>
</body>
</html>