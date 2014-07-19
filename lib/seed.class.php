<?php
   ///////////////////////////////////////////////////////////
   // ok outdoor 수집 기본이 되는 데이터를 읽어서 array로 리턴.
   public function fileReadToArray($filepath) {
      $crawl_list_arr = array();
      $buffer = "";

      $fp = fopen($filepath, "r") or die("$filepath : 파일열기에 실패 하였습니다!!!\n");
      while(!feof($fp)) {
         $buffer = fgets($fp);
         $data = trim($buffer);
         if (strlen($data) != 0) {
            array_push($crawl_list_arr, $buffer);
         }
      }
      fclose($fp);
      return $crawl_list_arr;
   }

   ///////////////////////////////////////////////////////////
   // keyword | crawl url을 array로 받아서 db에 등록한다.
   public function keywordAndUrlInsertToDB($arr_list, $cp_name, $db) {
      $db->connect();
      foreach($arr_list as $key => $value) {
         $item = explode("|", $value);
         $t_keyword = trim($item[0]);
         $t_url     = trim($item[1]);

         $s_sql = "SELECT url FROM SOCIAL_SHOP_CRAWL_T WHERE url = '$t_url'";
         if ($db->data_exist($s_sql) == 0) { // db에 link가 없다면 insert.
            $t_sql = "INSERT INTO SOCIAL_SHOP_CRAWL_T (keyword1, keyword2, keyword3, url, cp)
               VALUES ('$t_keyword', '', '', '$t_url', '$cp_name')";
            $db->select($t_sql);
            echo "(INSERT) $t_keyword\n";
            $this->total_insert_count++;
         }
         else { // db 에 이미 link가 저장되어 있다면 skip.
            echo "#SKIP# $t_keyword\n";
            $this->total_skip_count++;
         }
         $this->total_process_count++;
      }
      $db->commit();
      //$db->close();
   }
?>
