<?php

include "./s_lib/curl.class.php";
include "./s_lib/parser.class.php";
include "./s_lib/db.class.php";

///////////////////////// define variable //////////////////////////////
$query = '';
$search_request = "http://localhost:9200/_search?default_operator=AND";
$json_search = "http://localhost:9200/shop/_search";

$receve_result_count = 0;
$page_from = 0;
$page_size = 16;
$page_cp = "";
$param_suje = "";
$view_type = 1;

$curl = new EPCurl;

///////////////////////// request param //////////////////////////////
if (isset($_GET['q'])) {
	$query = $_GET["q"];
	if ($query == "") {
		echo "쿼리가 필요합니다.!!<br>";
		return 0;
	}	
} else {
	echo "쿼리가 필요합니다.!!<br>";
	return 0;
}

if (isset($_GET['size'])) {
	$t_size = $_GET["size"];
	if ($t_size != "") {
		$page_size = intval($t_size);
	}	
}

if (isset($_GET['from'])) {
	$t_from = $_GET["from"];
	if ($t_from != "") {
		$page_from = intval($t_from);
	}	
}

if (isset($_GET['cp'])) {
	$t_cp = $_GET["cp"];
	if ($t_cp != "") {
		$page_cp = $t_cp;
	}	
}

if (isset($_GET['type'])) {
	$t_type = $_GET["type"];
	if ($t_type != "") {
		$view_type = $t_type;
	}	
}

if (isset($_GET['suje'])) {
	$t_suje = $_GET["suje"];
	if ($t_suje != "") {
		$param_suje = $t_suje;
	}	
}

///////////////////////////////////////////////////////
// make request param.
/*
$query_string = '{
    "query": {
        "query_string": {
            "query": "'.$query.'",
            "fields": ["title", "cmt", "brand"]
        }
    }
}';
*/
/*
$query_string = '{
    "query" : {
        "query_string" : {
            "query":"'.$query.'",
            "fields": ["title","cate", "cmt","brand"]
        }
    },
    "sort": {
            "price_sale": {
                "order": "asc"
        }
    },
	"from": "'.$page_from.'",
	"size": "'.$page_size.'"
}';
*/
/*
$query_string = '{
    "query" : {
        "query_string" : {
            "query":"'.$query.'",
            "fields": ["title^3","cate", "cmt","brand"]
        }
    },
	"from": "'.$page_from.'",
	"size": "'.$page_size.'"
}';
*/

///////////////////////////////////////////////////////
// make request param.
$en_query = urlencode($query);
if ($page_cp != "") {
	$search_request = $search_request . '&q=' . $en_query . '%20AND%20cp:' . $page_cp;
} else {
	$search_request = $search_request . '&q=' . $en_query; 
}

$search_request = $search_request . '&size=' . $page_size . '&from=' . $page_from;

///////////////////////////////////////////////////////
// make request uri.
$g_req_uri = '&size='.$page_size.'&from='.$page_from.'&cp='.$page_cp.'&type='.$view_type;
$g_req_uri_no_type = '&size='.$page_size.'&from='.$page_from.'&cp='.$page_cp;

///////////////////////////////////////////////////////
// search JSON.
/*
$result   = $curl->requestPost2($json_search, $query_string);
$json_arr = json_decode($result, true);
$hits     = $json_arr['hits'];
$search_total = $hits['total'];
*/

///////////////////////////////////////////////////////
// search URI.
$result = file_get_contents($search_request);
$json_arr = json_decode($result, true);
$hits = $json_arr['hits'];
$search_total = $hits['total'];


///////////////////////////////////////////////////////
// print header.
prt_header($query);

///////////////////////////////////////////////////////
// print body.
prt_body($query, $view_type);

///////////////////////////////////////////////////////
// print result header.
prt_result_header($query, $search_total, $g_req_uri_no_type, $param_suje, $view_type);

///////////////////////////////////////////////////////
// print result list.
if ($view_type == 1) {
	$receve_result_count = prt_search_result_type1($hits, $query, $page_size);
} else if ($view_type == 2) {
	$receve_result_count = prt_search_result_type2($hits, $query, $page_size);
} else {
	$receve_result_count = prt_search_result_type1($hits, $query, $page_size);
}

///////////////////////////////////////////////////////
// pagination.
prt_pagination($query, $page_from, $page_size, $page_cp, $receve_result_count, $view_type, $param_suje);

///////////////////////////////////////////////////////
// print footer.
prt_footer();



///////////////////////////////////////////////// function ///////////////////////////////////////////////
///////////////////////////////////////////////// function ///////////////////////////////////////////////
/////////// get cp name
function get_cpname($name) {
	$cpname = "";
	switch ($name) {
		case "ok":
			$cpname = "오케이아웃도어";
			break;
		case "sb":
			$cpname = "SB CLUB";
			break;
		case "first":
			$cpname = "초캠몰";
			break;
		case "coupang":
			$cpname = "쿠팡";
			break;
		case "zeppelin":
			$cpname = "제플린아웃도어";
			break;
		case "campingmall":
			$cpname = "캠핑몰";
			break;
		default:
			$cpname = "캠핑";
	}
	
	return $cpname;
}

/////////// remove str
function rm_str($str) {
	$s = str_replace("[", " ", $str);
	$t = str_replace("]", " ", $s);
	
	return $t;
}


/////////// print result header.
function prt_result_header($query, $search_total, $req_uri, $suje, $type) {

echo '
<!-- Page Content -->
<div class="container">

	<!-- search from -->
	<div class="row">
		<div class="col-lg-12">
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group">
					<input type="text" name="q" class="form-control" placeholder="'.$query.'">
				</div>
				<button type="submit" class="btn btn-default">검색</button>
			</form>
		</div>
	</div>

	<!-- line -->
	<div class="row">
		<div class="col-lg-12">
			<hr>
		</div>
	</div> 
	<!-- /. line -->

	<!-- 등산화 검색어 서제스트 -->
	<div class="row">
		<div class="col-lg-12">
';
// -- echo.

if (strcmp($suje, "신발") == 0) {
	echo '
				<div class="btn-group">
					<button class="btn btn-success dropdown-toggle" data-toggle="dropdown">등산화 
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="./search.php?q=남성 등산화&type='.$type.'&suje='.$suje.'">남성 등산화</a></li>
						<li><a href="./search.php?q=여성 등산화&type='.$type.'&suje='.$suje.'">여성 등산화</a></li>
						<li class="divider"></li>
						<li><a href="./search.php?q=스포츠화&type='.$type.'&suje='.$suje.'">스포츠화</a></li>
						<li><a href="./search.php?q=아쿠아 슈즈&type='.$type.'&suje='.$suje.'">아쿠아슈즈</a></li>
						<li><a href="./search.php?q=남성 샌들&type='.$type.'&suje='.$suje.'">남성 샌들</a></li>
						<li><a href="./search.php?q=여성 샌들&type='.$type.'&suje='.$suje.'">여성 샌들</a></li>
					</ul>
				</div>
	';
	// -- echo.
}
// -- if.

echo '
			<!-- 정렬 옵션 -->
			<div class="btn-group">
				<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">결과 정렬 <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#">정확도</a></li>
					<li><a href="#">가격 낮은순</a></li>
					<li><a href="#">가격 높은순</a></li>
				</ul>
			</div>

			<!-- 보는 방법 옵션 -->
			<div class="btn-group">
				<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">보는 방법 <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="search.php?q='.$query.$req_uri.'&type=1&suje='.$suje.'">기본</a></li>
					<li><a href="search.php?q='.$query.$req_uri.'&type=2&suje='.$suje.'">여러개</a></li>
				</ul>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			검색어 : <strong>\''.$query.'\' </strong> 에 대하여 <strong>'.$search_total.'</srong> 개의 결과를 찾았습니다.
		</div>
	</div>
	<!-- /.row -->


	<hr>
';
}
// --  prt_result_header.


//////////////////////////////////////////////////////////////
/// print result list.
function prt_search_result_type1($hits, $query, $page_size) {

	$result_count = 0;

	foreach ($hits['hits'] as $list) {
		$print_price = '';
		$item = $list['_source'];
		$title = $item['title'];
		$link = $item['link'];
		$brand = $item['brand'];
		$thumb  = $item['thumb'];
		$cp_name  = $item['cp'];
		$price_sale  = $item['price_sale'];
		$price_org  = $item['price_org'];
		$cmt  = $item['cmt'];

		// 검색결과 몇건을 받았는지 count.
		$result_count++;

		// cp name
		$t_cpname = get_cpname($cp_name);

		// remove special char brand.
		$t_brand = rm_str($brand);

		// price.
      if (intval($price_sale) != 0 AND intval($price_org) != 0) {
         $print_price = '<h4><del><small>' . $price_org . '</small></del> -> <strong class="text-danger">' . $price_sale . '</strong></h4>';
      } else if (intval($price_sale) == 0 AND intval($price_org) != 0) {
         $print_price = ' <h4><strong class="text-danger">' .$price_org. '</strong></h4>';
      } else if (intval($price_sale) != 0 AND intval($price_org) == 0) {
         $print_price = ' <h4><strong class="text-danger">' . $price_sale . '</strong></h4>';
      } else {
         $print_price = "확인필요";
      }

		echo '
			<!-- Project One -->
			<div class="row">
				<div class="col-md-7">
				<!-- <a href="'.$link.'" target="_new"> -->
					<img class="img-responsive" src="'.$thumb.'" alt="'.$title.'">
					<!-- </a> -->
				</div>

				<div class="col-md-4">
					<h4>'.$title.'</h4>
					<a href="./search.php?q='.$query.' '. $t_brand.'&from=0&size='.$page_size.'&cp='.$cp_name.'"> 
						<span class="label label-default">'.$brand.'</span>
					</a>
					<a href="./search.php?q='.$query.'&from=0&size='.$page_size.'&cp='.$cp_name.'"> 
						<span class="label label-warning">'.$t_cpname.'</span>
					</a>
			';
		// -- echo

		echo $print_price;

		echo '
			<a class="btn btn-primary" href="'.$link.'" target="_new"> 상품 보러 가기 <span class="glyphicon glyphicon-chevron-right"></span></a>
			</div>
			</div>
			<!-- /.row -->

			<hr>
			';
		// -- echo.
	}
	// -- forearch.

	return $result_count;
}


//////////////////////////////////////////////////////////////
/// print result list type 2.
function prt_search_result_type2($hits, $query, $page_size) {

	$result_count = 0;

	echo '<div class="row">';

	foreach ($hits['hits'] as $list) {
		$print_price = '';
		$item = $list['_source'];
		$title = $item['title'];
		$link = $item['link'];
		$brand = $item['brand'];
		$thumb  = $item['thumb'];
		$cp_name  = $item['cp'];
		$price_sale  = $item['price_sale'];
		$price_org  = $item['price_org'];
		$cmt  = $item['cmt'];

		if (intval($price_sale) != 0 AND intval($price_org) != 0) {
			$print_price = '<del><small>' . $price_org . '</small></del> -> ' . $price_sale;
		} else if (intval($price_sale) == 0 AND intval($price_org) != 0) {
			$print_price = $price_org;
		} else if (intval($price_sale) != 0 AND intval($price_org) == 0) {
			$print_price = $price_sale;
		} else {
			$print_price = "확인필요";
		}

		// 검색결과 몇건을 받았는지 count.
		$result_count++;

		// cp name
		$t_cpname = get_cpname($cp_name);

		// remove special char brand.
		$t_brand = rm_str($brand);

		echo '
			<div class="col-lg-3 col-md-4 col-xs-6 thumb">
				<div class="thumbnail right-caption span4">
					<a class="thumbnail" href="'.$link.'" target="_new">
						<img class="img-responsive" src="'.$thumb.'" alt="'.$title.'">
						<div class="caption">
						'.$print_price.'<small> <br> ['.$t_cpname.']</small>
						</div>
					</a>
				</div>
			</div>
		';
		// -- echo.
	}
	// -- forearch.

	echo '</div>';

	return $result_count;
}


///////////////////////// print header //////////////////////////////
function prt_header($query) {
	echo '
		<!DOCTYPE html>
		<html lang="en">

		<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>'.$query.' - OutdoorLife&You</title>

		<!-- Bootstrap Core CSS -->
		<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="./bootstrap/css/1-col-portfolio.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		</head>
		';
}


///////////////////////// print body //////////////////////////////
function prt_body($query, $type) {

	echo '
		<body>

		<!-- Navigation -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="./search.php?q=*&type='.$type.'">OutdoorLife&You</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li>
							<a href="./search.php?q=텐트 OR 타프&type='.$type.'&suje=텐트">텐트/타프</a>
						</li>
						<li>
							<a href="./search.php?q=테이블 OR 체어 OR 의자 OR 가구&type='.$type.'&suje=가구">테이블/체어</a>
						</li>
						<li>
							<a href="./search.php?q=코펠 OR 식기 OR 수저 OR 칼 OR 도마 OR 나이프&type='.$type.'&suje=식기">코펠/식기</a>
						</li>
						<li>
							<a href="./search.php?q=침낭 OR 매트&type='.$type.'&suje=침낭">침낭/매트</a>
						</li>
						<li>
							<a href="./search.php?q=랜턴 OR 버너 OR 후레시 OR 스토브&type='.$type.'&suje=랜턴">랜턴/버너</a>
						</li>
						<li>
							<a href="./search.php?q=배낭 OR 가방 OR 수납가방&type='.$type.'&suje=가방">가방/배낭</a>
						</li>
						<li>
							<a href="./search.php?q=등산화 OR 신발&type='.$type.'&suje=신발">신발/등산화</a>
						</li>
						<li>
							<a href="#">About</a>
						</li>
					</ul>
				</div>
				<!-- /.navbar-collapse -->
			</div>
			<!-- /.container -->
		</nav>
	';
}


///////////////////////// print footer //////////////////////////////
function prt_footer() {
	echo '
		<!-- Footer -->
		<footer>
			<div class="row">
				<div class="col-lg-12">
					<p>Copyright &copy; OutdoorLife & You 2013</p>
					<p><small>outdoorlifenyou@gmail.com</small></p>
				</div>
			</div>
			<!-- /.row -->
		</footer>

	</div>
	<!------------------ /.container --------------------->
	<!------------------ /.container --------------------->

		 <!-- jQuery Version 1.11.0 -->
		 <script src="bootstrap/js/jquery-1.11.0.js"></script>

		 <!-- Bootstrap Core JavaScript -->
		 <script src="bootstrap/js/bootstrap.min.js"></script>

	</body>


	</html>
	';
}


///////////////////////// print pagination //////////////////////////////
function prt_pagination($query, $page_from, $page_size, $page_cp, $list_count, $type, $suje) {

	echo '
			  <!-- Pagination -->
			  <div class="row text-center">
					<div class="col-lg-12">
						<ul class="pager">
	';
	// -- echo.

	// 이전보기 구현
	if ($page_from > 0) {
		$cur_from = $page_from - $page_size;
		echo '
							<li>
								<a href="./search.php?q='.$query.'&from=0&size='.$page_size.'&cp='.$page_cp.'&type='.$type.'&suje='.$suje.'"> 처음으로 </a>
							</li>
							<li>
								<a href="./search.php?q='.$query.'&from='.$cur_from.'&size='.$page_size.'&cp='.$page_cp.'&type='.$type.'&suje='.$suje.'"> 이전 </a>
							</li>
		';
		// -- echo.
	}

	// 더보기 구현
	if ($list_count == $page_size) {
		$next_from = $page_from + $page_size;
		echo '
							<li>
								<a href="./search.php?q='.$query.'&from='.$next_from.'&size='.$page_size.'&cp='.$page_cp.'&type='.$type.'&suje='.$suje.'"> 다음 </a>
							</li>
		';
		// -- echo.
	}


	// pagination 마무리.
	echo '
						</ul>
					</div>
				</div>
				<!-- /.row -->

				<hr>
	';
	// -- echo.
}


?>
