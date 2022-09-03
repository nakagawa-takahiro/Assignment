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
    <?php echo Asset::css('style.css'); ?>
</head>
<body>

    <header style="color: white; background-color: #222222; top: 0; height: 3rem; padding-left: 1rem">
    <h1><?php echo "Signed in as $loginUser"; ?></h1>    
    <nav style="display: inline-block">
            <a href="/channel/index">チャンネル一覧</a>
            <a href="#">ブックマーク一覧</a>
            <a href="/auth/logout">ログアウト</a>
        </nav>

    </header>

    <h1>チャンネル選択画面</h1>
    <?php echo "Signed in as $loginUser"; ?>

    <div data-bind="foreach: channels" >
        <a id="link" href="#" data-bind="click: moveToChannel, text: channelname, value: channelname"></a><br>
    </div>

    <!-- <a href="/message/index">message</a> -->
    <div>
        <p>チャンネルを追加</p>
        <form method="POST" action="/channel/register/<?php echo $loginUser ?>">
            チャンネル名:<input type="text" name='channel'><br>
            <input type="submit" value="送信">

        </form>
    </div>

    <script type="text/javascript">
        let json = 
        '<?php
        $json=json_encode($data);
        echo $json;
        ?>';
        console.log(json);
        
        let obj = JSON.parse(json);
        console.log(obj);

        function myViewModel() {
            let self = this;
            self.channels = ko.observableArray(obj);

            self.moveToChannel = function(channel) {
                // console.log(channel['channelname']);
                let link = document.getElementById('link');
                let url = '<?php echo Uri::create('message/index/'); ?>'+channel['channelname'];
                link.setAttribute('href', url);
                // console.log(url);
                window.location.href = url;
            };
            

        }

        ko.applyBindings(myViewModel);

    </script>
</body>
</html>