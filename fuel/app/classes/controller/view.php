<?php
namespace Com;
class Controller_Settings extends Controller_Manage
{
  public function action_media_import()
  {
    if (!$this->data['account_data']['available_rpa']) :
      \Response::redirect(\Uri::base(). \Uri::segment_replace('/*/*/student'));
    endif;

    $company_id = parent::getcompany_id();

    $this->data['breadcrumbs'] = "媒体自動取り込み設定";
    $this->data['entry_roots'] = Model_Student::get_entryroots($company_id);
    $this->data['selections'] = Model_Progress::get_selection($company_id);
    $this->data['rpa_settings'] = Model_Rpa::get_rpa_setting($company_id);

    \Config::load('rpa', true);
    $this->data['event_match_condition_default'] =  \Config::get('rpa.event_match_condition_default');

    $view = \View::forge('settings/index')
      ->set(array(
        'content' => \View::forge('settings/media_import'),
        'submenu' => \View::forge('settings/submenu'),
        'header' => \View::forge('template/header'),
        'footer' => \View::forge('template/footer/footer_settings'),
        'footer_utility' => \View::forge('template/footer/footer_utility'),
      ));
    $view->set_global($this->data);

    return $view;
  }
}