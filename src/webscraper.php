<?php
  require 'vendor/autoload.php';
  require 'webscraper2.php';
  use GuzzleHttp\Client;
  $client = new Client([
      'base_uri' => 'http://archive-grbj-2.s3-website-us-west-1.amazonaws.com/',
      'timeout'  => 10.0,
    ]);
  $data = Article::get_articles($client);
  print_r ($data);
?>
