<?php
require_once 'session.php';
/**
 * Created by PhpStorm.
 * User: mobilesolution
 * Date: 8/16/18
 * Time: 11:16 AM
 */
define("TABLE_CRAWL", "c_all");
define("TABLE_CRAWL_EPISODES", "c_all_episodes");
define("TABLE_CONF", "config_np");

//DEV
/*define("INF_URL", "https://www.inflixer.com/");
//define("INF_API_URL", "https://api.inflixer.com/v4/");
define("INF_API_URL", "https://dev.inflixer.com/v6/");
define("INF_API_URL_HTTP", "http://dev.inflixer.com/v6/");*/

define("INF_URL", "http://localhost/");
//define("INF_API_URL", "https://api.inflixer.com/v4/");
define("INF_API_URL", "http://localhost/otp/");
define("INF_API_URL_HTTP", "http://localhost/otp/");

//define("DB_CRAWLER", "dev");
//define("DB_CRAWLER_USERNAME", "dev");
//define("DB_CRAWLER_PASS", "devbemobile04");

//define("DB_CRAWLER", "inflixer_robo");
//define("DB_CRAWLER_USERNAME", "robo");
//define("DB_CRAWLER_PASS", "234hrk345g883jdy7");
//
define("DB_CRAWLER", "dev_robov6");
define("DB_CRAWLER_USERNAME", "dev");
define("DB_CRAWLER_PASS", "devbemobile04");

//LOCAL
//define("DB_CRAWLER", "dev_robov5");
//define("DB_CRAWLER_USERNAME", "root");
//define("DB_CRAWLER_PASS", "");

//table gdrive
define("TABLE_CRAWL_ANIME", "c_gdrive_anime_inflixer_movie");
define("TABLE_CRAWL_ANIME_SERI", "c_gdrive_anime_inflixer_tv");
define("TABLE_CRAWL_ANIME_EPISODE", "c_gdrive_anime_tv_episode");


define("TMDB_API_KEY", "55545b0573918d0261261e2a7489eec2");
//define("INF_KEY", "2y10WOaEbnfSTnJ9NVUsDw5uKufDxsngv1QPREZaQt2f7vfrzhgEBbcZe");
define("INF_KEY", "2y10ZeA82P0VHPkwoRsv8soQeuBhjeJVHT2LjbymQqCFty8rJqFsz2y");
define("SESSION_ID", "2f1060f1438aa1bd9cc0f550fd4f3c12cb210c82");

define("INF_SEARCH_URL",    INF_URL . "typeahead/");
define("INF_FILM_URL",      INF_URL . "film/");
define("INF_SERI_URL",      INF_URL . "seri/");

// Api search
define("INF_SEARCH_MOVIE_URL",    INF_API_URL . "search/movie?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']."&query=");
define("INF_SEARCH_TV_URL",      INF_API_URL . "search/tv?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']."&query=");
define("INF_SEARCH_STORY_URL",      INF_API_URL . "search/story?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']."&query=");

// Api Scrape
define("INF_SCRAPE_URL",    INF_API_URL . "scrape/all?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']."&query=");
define("INF_VIDEO_ADD_URL",    INF_API_URL . "video_source/add?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_SCRAPE_FETCH_URL",    INF_API_URL . "scrape/fetch?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_SCRAPE_FETCH_STATUS_URL",    INF_API_URL . "scrape/status?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);

// API GET
define("INF_GET_TV_URL",    INF_API_URL . "tv/[Q]?api_key=".INF_KEY."&page=");
define("INF_GET_MOVIE_URL",    INF_API_URL . "movie/[Q]?api_key=".INF_KEY."&page=");

//API DETAILS MOVIE/TV
define("INF_MOVIE_DETAIL", INF_API_URL."movie/[ID]?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_TV_DETAIL", INF_API_URL."tv/[ID]?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_VIDEOSOURCE_UPDATE", INF_API_URL."video_label/add?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_VIDEOLABEL_ORDER", INF_API_URL."video_label?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);

//API SESSION
define("INF_CREATE_REQUEST_TOKEN", INF_API_URL."authentication/token/new?api_key=".INF_KEY);
define("INF_VALIDATE_WITH_LOGIN", INF_API_URL."authentication/token/validate_with_login?api_key=".INF_KEY);
define("INF_VALIDATE_WITH_GOOGLE", INF_API_URL."authentication/token/validate_with_google?api_key=".INF_KEY);
define("INF_VERIFY", INF_API_URL."account/verify?api_key=".INF_KEY);
define("INF_ACCOUNT", INF_API_URL."account?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_ACCOUNT_STORY", INF_API_URL."account/story?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);

//API INF TMDB
define("INF_ADD_MOVIE_TMDB",    INF_API_URL . "info/movie/tmdb?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_ADD_TV_TMDB",    INF_API_URL . "info/tv/tmdb?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);

//API TMDB
//define("TMDB_SEARCH_URL", "https://api.themoviedb.org/3/search/multi?api_key=55545b0573918d0261261e2a7489eec2&language=en-US&page=1&include_adult=false&query=");
define("TMDB_POSTER_URL", "https://image.tmdb.org/t/p/w342/");

define("TMDB_SEARCH_URL", "https://api.themoviedb.org/3/search/multi?api_key=55545b0573918d0261261e2a7489eec2&language=en-US&include_adult=false&query=");
define("TMDB_SEARCH_MOVIE", "https://api.themoviedb.org/3/search/movie?api_key=55545b0573918d0261261e2a7489eec2&language=en-US&include_adult=false&query=");
define("TMDB_SEARCH_TV", "https://api.themoviedb.org/3/search/tv?api_key=55545b0573918d0261261e2a7489eec2&language=en-US&include_adult=false&query=");
define("TMDB_TV_DETAIL", "https://api.themoviedb.org/3/tv/[ID]?api_key=55545b0573918d0261261e2a7489eec2&append_to_response=credits,videos,images,keywords,reviews,lists,similar");
define("TMDB_MOVIE_DETAIL", "https://api.themoviedb.org/3/movie/[ID]?api_key=55545b0573918d0261261e2a7489eec2&append_to_response=credits,videos,images,keywords,reviews,lists,similar");
define("TMDB_TV_SEASON", "https://api.themoviedb.org/3/tv/[ID]/season/[SEASON]?api_key=55545b0573918d0261261e2a7489eec2&append_to_response=credits");

//BASEURL 
define("INF_CHANGE_DOMAIN", INF_API_URL."video_label/domain/change?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
define("INF_LABEL_DOMAIN", INF_API_URL."video_label/domain?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']."&name=");
define("INF_GET_GENRES",    INF_API_URL . "genre?api_key=".INF_KEY."&session_id=".$_SESSION['session_id']);
