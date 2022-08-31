<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-1.8.3.js" integrity="sha256-dW19+sSjW7V1Q/Z3KD1saC6NcE5TUIhLJzJbrdKzxKc=" crossorigin="anonymous"></script>

<h1>ログイン成功</h1>

<!-- <div data-bind="foreach: { data: obj, as:'ob' }">
    <span data-bind="text: ob['username']"></span> <span data-bind="text: ob['posted_at']"></span><br>
    <span data-bind="text: ob['content']"></span><br>
    <br>
</div> -->

<!-- <div data-bind="foreach: { data: chats, as:'chat' }">
    <p data-bind="text: chat"></p>
</div> -->


<div data-bind="foreach: obj" >
    <span data-bind="text: username"></span> <span data-bind="text: content"></span>
    <br>    
</div>



<form action="" method="post">
    <input type="text" name="content">
    <button onclick="submitForm();">送信</button>
</form>

<script type="text/javascript">


    function submitForm(){
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
            // console.log("SUCCESS");
            console.log(data);

            function chatPost() {
                viewModel.username.push(data['username']);
                viewModel.content.push(data['content']);
            };


        }).fail(function() {
            alert("失敗");
            // console.log("FAILURE");
        });
    }

    let json = 
    '<?php
      $json=json_encode($data);
      echo $json;
    ?>';
    console.log(json);
    
    const obj = JSON.parse(json);
    console.log(obj);

    let chats = [];

    obj.forEach(function(element){
        // console.log(element['content']);
        chats.push(element['content']);

    });
    console.log(chats);



    let viewModel = {
        username: ko.observableArray(""),
        content: ko.observableArray("")
    };

    ko.applyBindings(viewModel);


//     let items = chats;

//     let SimpleListModel = function(chats) {
        
//         this.chats = ko.observableArray(chats);
//         this.itemToAdd = ko.observable("");
//         this.addItem = function() {
//             if (this.itemToAdd() != "") {
//                 this.chats.push(this.itemToAdd());
//                 this.itemToAdd("");
//             }
//         }.bind(this);

//     };
 

// ko.applyBindings(new SimpleListModel());

 


// ko.applyBindings({obj});

</script>
