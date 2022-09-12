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
    <h1>Signed in as '<?php echo $loginUser ?>'</h1>
    <nav style="display: inline-block">
        <a href="/channel/index">チャンネル一覧</a>
        <a href="/bookmark/index">ブックマーク一覧</a>
        <a href="/auth/logout">ログアウト</a>

    </nav>
</header>

    <main>
        <div>
            <p data-bind="text: username"></p>
            <p data-bind="text: self_introduction"></p>
            <p data-bind="text: url"></p>
        </div>
        <div>
            <a href="" data-bind="click: moveToDM, visible: isVisible()">DMはこちらのリンクから</a><br>
            <a href="" data-bind="click: editProf, visible: visible()">プロフィールを編集する</a>
        
            <form action="" method="post" data-bind="visible: showForm"  >
                <textarea type="text" id="content" placeholder="ここに文章を入力してください" data-bind="text: editContent"></textarea>
                <input type="text" id="url_link" placeholder="URL" data-bind="text: editUrl">
                <button data-bind="click: submitNewProf">完了</button>
            </form>

        </div>

        
    </main>
</body>

<script type="text/javascript">


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


    }


    ko.applyBindings(myViewModel);


</script>


</html>