<?php
/*
Plugin Name: WP SEO Status
Plugin URI: http://wp-docs.ru/dashboard-widget-seo-status
Description: Виджет для консоли, который подсчитывает текущие показатели ТИЦ(авторитетность по Яндексу) и Alexa Rank(посещаемость) вашего сайта.
Author URI: http://druweb.ru
Author: YandexBot.
Version: 1.0.0
*/

add_action( 'wp_dashboard_setup', 'add_wpss_dashboard_widget' );

function wpss_dashboard_widget() {
  $url = get_bloginfo('url');
  print '<ul>
          <li>ТИЦ: '. wpss_get_tiq($url). '</li>
          <li>Alexa Rank: '. wpss_get_alr($url). '</li>
         </ul>';
}

function add_wpss_dashboard_widget() {
  wp_add_dashboard_widget( 'wpss_dashboard_widget', 'WP SEO Status', 'wpss_dashboard_widget' );
}

/**
 * Функция подсчитывает ТИЦ, получая данные у Яндекса.
 * @param string $url - адрес сайта для проверки.
 */
function wpss_get_tiq($url) {
  $xml = file_get_contents('http://bar-navig.yandex.ru/u?ver=2&show=32&url='. $url);
  $t = $xml ? (int) substr(strstr($xml, 'value="'), 7) : 'N/A';
  return $t;
}

/**
 * Функция подсчитывает Alexa Rank.
 * @param string $url - адрес сайта для проверки.
 */
function wpss_get_alr($url) {
  $ar = new Get_Alexa_Ranking();
  return $ar->get_rank($url);
}

/**
 * PHP Class для подсчета Alexa Rank.
 * Внимание: для работы требуется cURL.
 * @author http://www.paulund.co.uk
 */
class Get_Alexa_Ranking {
  /**
  * Get the rank from alexa for the given domain
  *
  * @param $domain
  * The domain to search on
  */
  public function get_rank($domain){
    $url = "http://data.alexa.com/data?cli=10&dat=snbamz&url=".$domain;

    //Initialize the Curl
    $ch = curl_init();

    //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);

    //Set the URL
    curl_setopt($ch, CURLOPT_URL, $url);

    //Execute the fetch
    $data = curl_exec($ch);

    //Close the connection
    curl_close($ch);

    $xml = new SimpleXMLElement($data);

    //Get popularity node
    $popularity = $xml->xpath("//POPULARITY");

    //Get the Rank attribute
    $rank = (string)$popularity[0]['TEXT'];

    if ( empty($rank) ) $rank = 'N/A';

    return $rank;
  }
}

?>