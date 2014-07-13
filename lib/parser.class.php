<?php

class EPParser {
   ///////////////////////////////////////////////////////////
   public function getBody($data, $stag, $etag) {
      mb_internal_encoding("UTF-8");
      $spos = mb_strpos($data, $stag);
      $epos = mb_strpos($data, $etag);
      if ($spos <= 0 || $epos <= 0)
         return '';

      $slen = mb_strlen($stag);

      $start = $spos + $slen;
      $end = $epos;
      $cut_size = $end - $start;

      $body = mb_substr($data, $start, $cut_size);
      return $body;
   }

   ///////////////////////////////////////////////////////////
   public function getList($data, $stag, $etag) {
      mb_internal_encoding("UTF-8");

      $list_ar = array();
      $spos = 0;
      $cur_pos = 0;
      $cut_size = 0;
      $list = "";
      $slen = mb_strlen($stag);


      for (;;)
      {
         $spos = mb_strpos($data, $stag, $cur_pos);
         $epos = mb_strpos($data, $etag, $spos+1);
         if ($spos <= 0 || $epos <= 0)
            break;

         $cut_size = $epos - $spos;
         $list = mb_substr($data, $spos + $slen, $cut_size);
         array_push($list_ar, $list);
         $cur_pos = $epos + 1;
      }

      return $list_ar;
   }

   ///////////////////////////////////////////////////////////
   public function getItem($data, $start_tag, $end_tag) {
      mb_internal_encoding("utf-8");
      $start_len = mb_strlen($start_tag);
      if ($start_len <= 0)
         return "TAG LENGTH ERROR";

      $start_pos = mb_strpos($data, $start_tag);
      if ($start_pos == 0)
         return "START_POS_NOT";

      $end_pos = mb_strpos($data, $end_tag, $start_pos + $start_len);
      if ($end_pos == 0)
         return "END_POS_NOT";

      $cut_size = $end_pos - $start_pos - $start_len;
      $result = mb_substr($data, $start_pos + $start_len, $cut_size);

      return $result;
   }

   ///////////////////////////////////////////////////////////
  // item 추출시 tag로만 구분하기 힘든경우 몇번째 tag에서부터 추출해라...라는 함수임.
  // $start_tag_next 몇번째가 start tag pos 라는 뜻.
  // $start_tag_next 는 1 이상이어야 함.
   public function getItemPos($data, $start_tag, $start_tag_next, $end_tag) {
      mb_internal_encoding("utf-8");
      $start_len = mb_strlen($start_tag);
      if ($start_len <= 0)
         return "TAG LENGTH ERROR";

		if ($start_tag_next == 0) $start_tag_next = 1;
		if ($start_tag_next > 0) {
			$pos = 0;
			$count = $start_tag_next;
			for (;;) {
				$start_pos = mb_strpos($data, $start_tag, $pos);
				if ($start_pos == 0)
					return "START_POS_NOT";

				if ($count == 1) break;
				$count--;
				$pos = $start_pos + $start_len;
			}
		}

		$end_pos = mb_strpos($data, $end_tag, $start_pos + $start_len);
		if ($end_pos == 0)
			return "END_POS_NOT";

      $cut_size = $end_pos - $start_pos - $start_len;
      $result = mb_substr($data, $start_pos + $start_len, $cut_size);

      return $result;
   }

} // class

?>
