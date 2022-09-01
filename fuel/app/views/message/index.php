<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-1.8.3.js" integrity="sha256-dW19+sSjW7V1Q/Z3KD1saC6NcE5TUIhLJzJbrdKzxKc=" crossorigin="anonymous"></script>

<h1>ログイン成功</h1>

<div data-bind="foreach: message" >
    <span data-bind="text: username"></span> <span data-bind="text: posted_at"></span><br>
    <span data-bind="text: content"></span><br>
    <br>
</div>

<br>

<form action="" method="post">
    <input type="text" name="content" data-bind='value: form, valueUpdate: "afterkeydown"'>
    <button onclick="submitForm();">送信</button>
</form>

<script type="text/javascript">


    function submitForm(){
        event.preventDefault();
        let username = '<?php echo $loginUser; ?>';
        let content = $('input[name=content]').val();
        let formData = {
            username: username,
            content: content
        };
        console.log(formData);

        $.ajax({
            url: '<?php echo Uri::create('chat/chat_post.json'); ?>',
            type: 'POST',
            cache: false,
            dataType : 'json',
            data: formData,

        }).done(function(data) {
            alert("成功");
            console.log("===========================================");
            console.log(data);

            myViewModel.message.push(data);
            myViewModel.form("");


        }).fail(function() {
            alert("失敗");
        });
    }

    let json = 
    '<?php
      $json=json_encode($data);
      echo $json;
    ?>';
    console.log(json);
    
    let obj = JSON.parse(json);
    console.log(obj);

    let chats = [];


var myViewModel = {
    message: ko.observableArray(obj),
    form: ko.observable("")
};

ko.applyBindings(myViewModel);


</script>
