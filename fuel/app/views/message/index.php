<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-1.8.3.js" integrity="sha256-dW19+sSjW7V1Q/Z3KD1saC6NcE5TUIhLJzJbrdKzxKc=" crossorigin="anonymous"></script>
<?php echo \Security::js_fetch_token(); ?>

<h1>ログイン成功</h1>
<div data-bind="foreach: message" >
    <span data-bind="text: username, value: username"></span> <span data-bind="text: posted_at"></span><br>
    <span data-bind="text: content, value: content"></span><br>
    <a href="#" data-bind="click: $parent.editChat">編集</a>
    <a href="#" data-bind="click: $parent.deleteChat">削除</a>
    <br>
    <br>
</div>




<br>

<form action="" method="post" data-bind="visible: showForm">
    <input type="text" name="content1" data-bind='value: form1, valueUpdate: "afterkeydown"' placeholder="ここにメッセージを入力してください">
    <button data-bind="click: submitMessage">送信</button>
</form>

<form action="" method="post" data-bind="visible: showEditForm">
    
    <span>メッセージの編集中です</span> <a href="#" data-bind="click: editStop">取消</a><br>
    <input type="text" name="content2" data-bind='value: form2, valueUpdate: "afterkeydown"' placeholder="ここにメッセージを入力してください">
    <button data-bind="click: submitNewMessage" >送信</button>
</form>

<script type="text/javascript">

let json = 
    '<?php
      $json=json_encode($data);
      echo $json;
    ?>';
    console.log(json);
    
    let obj = JSON.parse(json);
    console.log(obj);


    var myViewModel = {
    message: ko.observableArray(obj),
    form1: ko.observable(""),
    form2: ko.observable(""),
    showEditForm: ko.observable(false),
    showForm: ko.observable(true)
    };


    myViewModel.submitMessage = function (){
        event.preventDefault();
        let username = '<?php echo $loginUser; ?>';
        let content = $('input[name=content1]').val();
        let formData = {
            'username': username,
            'content': content,
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
            console.log(data);

            myViewModel.message.push(data);
            myViewModel.form1("");
            // myViewModel.showEditForm(true);
            // myViewModel.showForm(false);


        }).fail(function() {
            alert("失敗");
        });
    };





    myViewModel.deleteChat = function(msg) {

            event.preventDefault();
            let id = msg['id'];
            console.log(msg['id']);

            let formData = {
                'id': id,
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

                myViewModel.message(data);

            }).fail(function() {
                alert("失敗");
            });


        };

    let editChatId = 0;

    myViewModel.editChat = function(msg) {

        editChatId = msg['id'];
        // console.log(editChatId);
        event.preventDefault();
        myViewModel.form2(msg['content']);
        myViewModel.showEditForm(true);
        myViewModel.showForm(false);

    };

    myViewModel.submitNewMessage = function() {
        event.preventDefault();
            let id = editChatId;
            let content = $('input[name=content2]').val();
            
            let formData = {
                'id': id,
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
                console.log(data);

                myViewModel.message(data);
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
        myViewModel.showForm(true);
        alert("メッセージの編集を中断しました。")

    }

    ko.applyBindings(myViewModel);


</script>
