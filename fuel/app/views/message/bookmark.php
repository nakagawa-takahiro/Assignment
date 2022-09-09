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
        <nav style="display: inline-block">
            <a href="/channel/index">チャンネル一覧</a>
            <a href="/bookmark/index">ブックマーク一覧</a>
            <a href="/auth/logout">ログアウト</a>
        </nav>
    </header>

    <main style="padding: 1rem; margin-top: 2.5rem">
    <div data-bind="foreach: message" >
            <span style="padding: 1rem; font-size: 20px" data-bind="text: username, value: username"></span> 
            <span data-bind="text: posted_at"></span><br>
            <div style="border: solid black 1px; padding: 1rem">
                <span data-bind="text: content, value: content"></span>
            </div>
            <a href="">ブックマークから削除する</a>
            <br>
        </div>
    </main>

</body>

<script type="text/javascript">

let obj = 
        <?php
        $json=json_encode($data,JSON_PRETTY_PRINT);
        echo $json;
        ?>;
        console.log(obj);

    let myViewModel = {
        message: ko.observableArray(obj)
    };

    ko.applyBindings(myViewModel);

</script>

</html>



