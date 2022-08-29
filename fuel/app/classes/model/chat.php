<?php 

class Model_Chat extends \Orm\Model
{
    protected static $_table_name = 'table1';
    protected static $_properties = array(
        'username', 
        'password', 
        'chat_content', 
        'posted_at',
    );
}
