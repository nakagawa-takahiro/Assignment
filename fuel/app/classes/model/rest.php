<?php
namespace Mid;
class Model_Rpa extends \Model
{
  /**
   * 媒体自動取り込み設定を保存
   *
   * @param int   $company_id 企業ID
   * @param array $post       登録するデータ
   * @return array 結果
   */
  public static function set_rpa_setting($company_id, $post)
  {
    if(!empty($post['rpa_setting_id'])) : // 更新
      $query = \DB::update('rpa_setting')->set(array(
          'entry_root_id' => $post['entry_root_id'],
          'media_job_name' => trim($post['media_job_name']),
          'selection_step_id' => $post['selection_step_id'],
          'job_information_for_ic_id' => $post['job_information_for_ic_id'],
          'edited_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ))
        ->where('id', $post['rpa_setting_id'])
        ->where('company_id', $company_id);
    else : // 新規
      $rpa_settings = self::get_rpa_setting($company_id);

      $query = \DB::insert('rpa_setting')->set(array(
        'company_id' => $company_id,
        'entry_root_id' => $post['entry_root_id'],
        'media_job_name' => trim($post['media_job_name']),
        'selection_step_id' => $post['selection_step_id'],
        'job_information_for_ic_id' => $post['job_information_for_ic_id'],
        'order' => count($rpa_settings) + 1,
        'created_at' => date('Y-m-d H:i:s'),
      ));
    endif;

    $result = $query->execute();

    return $result;
  }
}