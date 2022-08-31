<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


<h1>ログイン成功</h1>

<div data-bind="foreach: { data: obj, as:'ob' }">
    <span data-bind="text: ob['username']"></span> <span data-bind="text: ob['posted_at']"></span><br>
    <span data-bind="text: ob['content']"></span><br>
    <br>
    
</div>

<div data-bind="foreach: { data: chats, as:'chat' }">
    <p data-bind="text: chat"></p>
</div>


<form action="" method="post" data-bind="submit: addItem">
    <input type="text" name="content" data-bind='value: itemToAdd, valueUpdate: "afterkeydown"'>
    <button onclick="submitForm();" data-bind="enable: itemToAdd().length > 0">送信</button>
    <!-- <p multiple="multiple" width="50" data-bind="text: items"> </p> -->
    
</form>

<script type="text/javascript">


    function submitForm(){
        let username = "user1";
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
            // alert('成功');

        }).fail(function() {
            // alert('失敗');
        });
    }

    let json = 
    '<?php
      $json=json_encode($data);
      echo $json;
    ?>'
    console.log(json)
    
    const obj = JSON.parse(json);
    console.log(obj);

    let chats = [];

    obj.forEach(function(element){
        // console.log(element['content']);
        chats.push(element['content']);

    });
    console.log(chats);


    let items = chats;

    let SimpleListModel = function(chats) {
        
        this.chats = ko.observableArray(chats);
        this.itemToAdd = ko.observable("");
        this.addItem = function() {
            if (this.itemToAdd() != "") {
                this.chats.push(this.itemToAdd());
                this.itemToAdd("");
            }
        }.bind(this);

    };
 

ko.applyBindings(new SimpleListModel());

</script>
