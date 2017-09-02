<?php
namespace FastTrackIndia\UpworkNewsScraper
class Article
{
  
	public static function get_articles($client,$params = array()) { 
    $data = array();
    $track_author = array();
    
    $body = Article::get_page($client , "/");

    #$body = read();
    #print $body;
    preg_match_all ( "/class\W+[^\"]*record\W.+?(?=(class\W+[^\"]*record\W|<\/body>))/is" , $body, $matches);
    $matches = $matches[0];
    //print_r($matches);
    foreach ($matches as $block) {
      if (preg_match("/<div[^>]+class\W+author.+?href=\"([^\"]+)/is",$block,$match) ) {
        $author_url = $match[1];
        //print $author_url;

        if (preg_match("/<h2[^>]+class\W+headline.+?href=\"([^\"]+)/is",$block,$match) ) {
          $article_url = $match[1];
          
          $article = Article::get_article($client, $article_url);
          if (isset($article['author_id']) ) {
            
            $author_id = $article['author_id'];
            if ( isset($track_author[$author_id]) ) {
              $pos = $track_author[$author_id]['pos'];
            }
            else {
              array_push($data,array('author_name' => $article['author_name'], 'articles'=>array() ));
              $pos = count($data) - 1;
              $track_author[$author_id]['pos'] = $pos;
              $track_author[$author_id]['num_articles'] = 0;

            }
            array_push($data[$pos]['articles'],$article);
            $track_author[$author_id]['num_articles']++;
          }
        }
      }
    }

    return $data;
	}

  public static function get_article($client, $article_url) {
    $body = Article::get_page($client, $article_url);
    $article = array();
    $article['article_url'] = $article_url;
    if(preg_match("/<h1[^>]+class\W+headline\W+>(.*?)<\/h1>/is",$body,$match)){

      $article['article_title'] = $match[1];
    }
    elseif(preg_match("/<h1[^>]*>(.*?)<\/h1>/is",$body,$match)){

      $article['article_title'] = $match[1];
    }
    if(preg_match("/<\/h1>.+?<div[^>]+class\W+date\W+>(.+?)<\/div>/is",$body,$match)){

      $article['article_date'] = $match[1];
    }


    if(preg_match("/<div[^>]+class\W+author-info.+?<h2[^>]*>.*?<a[^>]+>(.+?)<\/a>/is",$body,$match)){

      $article['author_name'] = $match[1];

      if(preg_match("/href=(\"|\')([^\'\"]+)[^>]+>/is",$match[0],$match)){

        $article['author_url'] = $match[2];

        if(preg_match("/\d+/is",$match[2],$match)){

          $article['author_id'] = $match[0];
        }
      }
    }
    if(preg_match("/<div[^>]+class\W+author_bio[^>]+>(.+?)<\/div>/is",$body,$match)){

      $article['author_bio'] = $match[1];

      if(preg_match("/<a[^>]+href\W+([^\'\"]+)twitter\.com([^\'\"]+)[^>]+>(.+?)<\/a>/is",$match[1],$twitter)){

        $article['author_twitter'] = $twitter[3];
      }
    }
    return $article;
    
  }
  
   public static function get_page($client, $url, $tries = 3) {
    for ($i =1 ;$i<=$tries;$i++) {
      try {
        $response = $client->request('GET', $url);
        return $response->getBody();
      }
      catch(Exception $e) {}
    }
    return "";
  }

}
?>