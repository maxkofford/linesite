<?php
namespace simplehtmldom_1_9_1;
require_once (__DIR__ . "/../apptop.php");
if(\core\Permissions::permission_level() != \core\Permissions::admin){
    \core\HTML::Redirect("/linesite/crud/crud_read_multiple.php");
}
// example of how to use advanced selector features
include('simple_html_dom.php');
$url = 'https://sedar.com/FindCompanyDocuments.do';

$company_name = "Allegiant";

$from_month = "01";
$from_day = "01";
$from_year = "2021";

$to_month = "02";
$to_day = "01";
$to_year = "2021";

$post_fields_array = ['company_search' => $company_name,
'document_selection' => 8, //news release
'industry_group' => "A",
'FromMonth' => $from_month,
'FromDate' => $from_day,
'FromYear' => $from_year,
'ToMonth' => $to_month,
'ToDate' => $to_day,
'ToYear' => $to_year,
'Variable' => "Issuer",
'lang' => "EN",
"page_no" => "1",
];

//$request_data = \core\HTTP_Request_Helper::post_request($url, $post_fields_array, ['Content-Type: text/html;charset=ISO-8859-1', 'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0'],  [], false);


echo $request_data;

//1287390 
//Allegiant
// -----------------------------------------------------------------------------
// descendant selector
// $str = <<<HTML
// <div>
//     <div>
//         <div class="foo bar">okdoke</div>
//     </div>
// </div>
// HTML;

// $html = str_get_html($str);
// echo $html->find('div div div', 0)->innertext . '<br>'; // result: "ok"
