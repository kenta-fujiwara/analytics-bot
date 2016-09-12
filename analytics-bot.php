<?php
// ライブラリの読み込み
require_once 'google-api-php-client/src/Google/autoload.php';

// サービスアカウントのメールアドレス
$service_account_email = 'analytics-bot@analytics-bot-143204.iam.gserviceaccount.com';

// 秘密キーファイルの読み込み
$key = file_get_contents('analytics-bot-e105379956cc.p12');

// プロファイル(ビュー)ID
$profile = '129347988';

// Googleクライアントのインスタンスを作成
$client = new Google_Client();
$analytics = new Google_Service_Analytics($client);

// クレデンシャルの作成
$cred = new Google_Auth_AssertionCredentials(
    $service_account_email,
    array(Google_Service_Analytics::ANALYTICS_READONLY),
    $key
);
$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {
  $client->getAuth()->refreshTokenWithAssertion($cred);
}

function getWeeklyRanking($analytics, $profile){
  $results = $analytics->data_ga->get(
    'ga:' . $profile,
    '7daysAgo',
    'yesterday',
    'ga:pageviews',
    array(
      'dimensions'  => 'ga:pageTitle',  // データの区切り
      'sort'        => '-ga:pageviews', // ページビューでソート
      'max-results' => '10',            // 取得件数
    )
  );

  // 取得したデータから必要な部分を抽出
  $data = $results->rows;

  // 7日前と昨日の日付を取得
  $start = date('n/d', strtotime('-1 week'));
  $end   = date('n/d', strtotime('-1 day'));

  // 配列で取得したデータをループで回してランキングに
  $ranking = $start . '〜' . $end . 'の記事ランキング' . "\n";
  foreach ($data as $key => $row) {
    $ranking .= ($key + 1) . '.' . $row[0] . ' ' . $row[1] . 'PV' . "\n";
  }

  return $ranking;
}

$ranking = getWeeklyRanking($analytics, $profile);

print_r($ranking);
?>
