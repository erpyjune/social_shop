<?php

include "../curl.class.php";
include "../parser.class.php";
include "../db.class.php";

$pre_url = "http://localhost:9200/_search?default_operator=AND&size=30&q=%s";

$query = $_GET["q"];
if ($query == "") {
   echo "쿼리가 필요합니다.!!<br>";
   return 0;
}

$en_query = urlencode($query);
$search_request = sprintf($pre_url, $en_query);
//$search_request = $search_request . '%20AND%20cp:ok';
//$search_request = sprintf($pre_url,$en_query, $en_query, $en_query, $en_query);
echo "query -> $search_request\n";
$result = file_get_contents($search_request);
$json_arr = json_decode($result, true);
//print_r($json_arr);
$hits = $json_arr['hits'];


///////////////////////// header //////////////////////////////
echo '
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>1 Col Portfolio - Start Bootstrap Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/1-col-portfolio.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
';

///////////////////////// body //////////////////////////////
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
                <a class="navbar-brand" href="#">Start Bootstrap</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#">About</a>
                    </li>
                    <li>
                        <a href="#">Services</a>
                    </li>
                    <li>
                        <a href="#">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
';


///////////////////////// list header //////////////////////////////
echo '

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Page Heading
                    <small>Secondary Text</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->
';

///////////////////////// list //////////////////////////////
$print_item_count = 0;

foreach ($hits['hits'] as $list) {
   $item = $list['_source'];
   $title = $item['title'];
   $link = $item['link'];
   $thumb  = $item['thumb'];
   $cp_name  = $item['cp'];
   $price_sale  = $item['price_sale'];
   $price_org  = $item['price_org'];
   $cmt  = $item['cmt'];


   if ($print_item_count == 0) {
		echo '
        <!-- Projects Row -->
        <div class="row">
		';
   }

	echo '
            <div class="col-md-4 portfolio-item">
                <a href="'.$link.'" target_"_new">
                    <img class="img-responsive" src="'.$thumb.'" alt="">
                </a>
                <h3>
                    <a href="'.$link.'">'.$title.'</a>
                </h3>
                <p>'.$cmt.'</p>
            </div>
';

   if ($print_item_count == 3) {
      echo '</div>'."<!-- end row -->\n";
      echo '<div class="row">'."\n";
      $print_item_count = 0;
   }

   $print_item_count++;

} // forearch.


///////////////////////// pagination //////////////////////////////
echo '
        <!-- Pagination -->
        <div class="row text-center">
            <div class="col-lg-12">
                <ul class="pagination">
                    <li>
                        <a href="#">&laquo;</a>
                    </li>
                    <li class="active">
                        <a href="#">1</a>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li>
                        <a href="#">5</a>
                    </li>
                    <li>
                        <a href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.row -->

        <hr>
';


///////////////////////// footer //////////////////////////////
echo '
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Your Website 2014</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
';

?>
