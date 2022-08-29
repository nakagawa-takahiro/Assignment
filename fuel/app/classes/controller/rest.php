<?php
namespace Mid;
class Controller_Rest_Rpa extends \Controller_Rest
{
  public function post_set_rpa_setting()
  {
    // トークンチェック
    if (!\Security::check_token()) :
      $res = array(
        'error' => 'セッションが切れている可能性があります。もう一度登録ボタンを押すか、ページを読み込み直してください。'
      );
      return $this->response($res);
    endif;

    $auth = \Auth::forge(array('driver'=>'Companyauth'));
    $company_id = $auth->get('company_id');
    $post = \Input::post();

    $result = Model_Rpa::set_rpa_setting($company_id, $post);

    if ($result) :
      // 登録したレコードをviewに返す
      $rpa_setting_id = !empty($post['rpa_setting_id']) ? $post['rpa_setting_id'] : $result[0];
      $rpa_setting = Model_Rpa::get_rpa_setting($company_id, $rpa_setting_id);

      $res = array(
        'success' => true,
        'rpa_setting' => current($rpa_setting),
      );
    else :
      $res = array(
        'error' => true,
      );
    endif;

    return $this->response($res);
  }
}