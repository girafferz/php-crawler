<?php
// thisfile.php

// 作業ディレクトリ
// $ mkdir ~/fetch_test/
// $ cd ~/fetch_test/
//
// composerインストール
// https://codezine.jp/article/detail/7827
// $ curl -sS https://getcomposer.org/installer | php
//
// goutteインストール
// http://blog.asial.co.jp/1316
// $ php composer.phar require fabpot/goutte:~2.0
//
// 実行
// $ php thisfile.php "https://kirindo.tmall.hk/search.htm?spm=a1z10.3-b.w4011-5996267154.90.ZquzGL&search=y&orderType=defaultSort&tsearch=y"
//
// 確認(文字コード:utf-8)
// $ cat output.tsv
//
// google spreadsheetにoutput.tsvをupload
//

require_once __DIR__ . '/vendor/autoload.php';
$client = new Goutte\Client();

$url = "https://kirindo.tmall.hk/search.htm?spm=a1z10.3-b.w4011-5996267154.90.ZquzGL&search=y&orderType=defaultSort&tsearch=y";
$url = $argv[1];

$crawler = $client->request('GET', $url);
$crawler = $crawler->filter('.item5line1');
$crawler = $crawler->filter('.item');

$stack = array();
$crawler->each(function($node, $num) use (&$stack) {
  $one = array();
  $detail = $node->filter('.detail');
  $a = $detail->filter('a');
  $cprice_area = $detail->filter('.cprice-area');
  $sale_area = $detail->filter('.sale-area');

  $price = trim($cprice_area->text());
  $desc = trim($a->text());
  $total_sales = trim($sale_area->text());

  array_push($one, $num, $price, $desc, $total_sales);

  if ($node->filter('.rates')->count()) {
      $rates = $node->filter('.rates')->filter('a');
      $rating = trim($rates->text()) ;
      array_push($one, $rating);
  }
  array_push($stack, $one);
});

$filename = "output.tsv";
foreach ($stack as $line) {
  $l = implode($line, "\t"). "\n";
  //UTF-8形式で書き出し
  //macはUTF-16, winはsjis-winに変換するとexcelで開ける
  //google spreadsheetsにuploadする場合はそのまま読める
  file_put_contents($filename, $l, FILE_APPEND);
}


