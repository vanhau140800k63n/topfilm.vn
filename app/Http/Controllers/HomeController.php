<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Constants\AdvancedSearch;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function getHomePage()
    {
        return view('pages.home');
    }

    public function searchMovie($key)
    {
        return view('pages.search', compact('key'));
    }

    public function searchByKeywordAjax($key)
    {
        $movieService = new MovieService();

        $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        while ($movieSearchWithKey == null) {
            $movieSearchWithKey = $movieService->searchWithKeyWord($key);
        }

        $output = '<div class="listfilm" style="width: 100%;">
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>Tìm kiếm cho từ khóa: ' . $key . '</span>
				</div>
			</div>
			<div class="recommend__item">';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movieSearchWithKey['searchResults'] as $movie) {
            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';

            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
            }

            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
            }
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
            $output .= '
					<a href="' . $route . '" class="card__film">
					<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['name'];
            if (isset($movie_check->year) && $movie_check->year != '') {
                $output  .= " (" . $movie_check->year . ")";
            }
            $output .= '</p>
                    </a>';
        }

        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        return response()->json($output);
    }


    public function searchMovieAdvanced($value)
    {
        $pos['a'] = strpos($value, 'a');
        $pos['b'] = strpos($value, 'b');
        $pos['c'] = strpos($value, 'c');
        $pos['d'] = strpos($value, 'd');

        $index['a'] = substr($value, $pos['a'] + 1, $pos['b'] - $pos['a'] - 1);
        $index['b'] = substr($value, $pos['b'] + 1, $pos['c'] - $pos['b'] - 1);
        $index['c'] = substr($value, $pos['c'] + 1, $pos['d'] - $pos['c'] - 1);
        $index['d'] = substr($value, $pos['d'] + 1);

        $search_advance_list = AdvancedSearch::SEARCH_LIST;
        $param['a'] = $search_advance_list[$index['a']]['params'];
        $param['b'] = '';
        $param['c'] = '';
        $param['d'] = '';

        if (!empty($index['b'])) {
            $param['b'] = $search_advance_list[$index['a']]['screeningItems'][0]['items'][$index['b']]['params'];
        }

        if (!empty($index['c'])) {
            $param['c'] = $search_advance_list[$index['a']]['screeningItems'][1]['items'][$index['c']]['params'];
        }

        if (!empty($index['d'])) {
            $param['d'] = $search_advance_list[$index['a']]['screeningItems'][2]['items'][$index['d']]['params'];
        }

        return view('pages.search_advance', compact('value', 'index', 'param'));
    }

    public function searchMovieAdvancedFirst(Request $req)
    {
        $movieService = new MovieService();

        $movie_search_advanced = $movieService->searchAdvanced($req);
        while ($movie_search_advanced == null) {
            $movie_search_advanced = $movieService->searchAdvanced($req);
        }

        $output = '<div class="listfilm" style="width: 100%;">
		<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>Tìm kiếm cho từ khóa: </span>
				</div>
			</div>
			<div class="recommend__item">';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_search_advanced['searchResults'] as $movie) {
            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';

            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
            }

            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
            }
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
            $output .= '
					<a href="' . $route . '" class="card__film">
					<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['name'];
            if (isset($movie_check->year) && $movie_check->year != '') {
                $output  .= " (" . $movie_check->year . ")";
            }
            $output .= '</p>
				</a>';
        }

        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        $count = count($movie_search_advanced['searchResults']);

        if ($count == 0) {
            return false;
        }

        $output .= '</div>
        <div class="text-center">
			<div class="lds-facebook">
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
        <div id="info" count="' . $count . '" sort="' . $movie_search_advanced['searchResults'][$count - 1]['sort'] . '"></div>
		       </div>
	        </div>';

        $data = [0 => $output, 1 => $count];

        return response()->json($data);
    }

    public function searchMovieAdvancedMore(Request $req)
    {
        $movieService = new MovieService();

        $movie_search_advanced = $movieService->searchAdvanced($req);
        while ($movie_search_advanced == null) {
            $movie_search_advanced = $movieService->searchAdvanced($req);
        }

        $output = '';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_search_advanced['searchResults'] as $movie) {
            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . '.jpg';

            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['domainType'] . $movie['id']] = $movie['coverVerticalUrl'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['name']];
            }
            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);
            $output .= '
					<a href="' . $route . '" class="card__film">
					<img class="image" src="' . asset($urlImage) . '" alt="image" />
					<p class="film__name">' . $movie['name'];
            if (isset($movie_check->year) && $movie_check->year != '') {
                $output  .= " (" . $movie_check->year . ")";
            }
            $output .= '</p>
                    </a>';
        }

        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        $count = count($movie_search_advanced['searchResults']);

        if ($count == 0) {
            return false;
        }

        $info = '<div id="info" count="' . $count . '" sort="' . $movie_search_advanced['searchResults'][$count - 1]['sort'] . '"></div>';

        $data = [0 => $output, 1 => $info, 2 => $count];

        return response()->json($data);
    }

    public function searchMoreMovie($page, $id)
    {
        $movieService = new MovieService();

        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=' . $page;
        $movie_home = $movieService->getData($url_movie);

        while ($movie_home == null) {
            $movie_home = $movieService->getData($url_movie);
        }

        $result = [];
        foreach ($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems) {
            if ($keyRecommendItems == $id) {
                $result = $recommendItems;
            }
        }
        return view('pages.moremovie', compact('result'));
    }

    public function searchKey(Request $req)
    {
        $key = $req->input('keyword');
        return redirect()->route('search', $key);
    }

    public function getHomeAjax(Request $req)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=' . $req->page,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'lang: vi',
                'versioncode: 11',
                'clienttype: ios_jike_default',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $convert = json_decode($response, true);

        $output = '';

        if (!empty($convert['data'])) {
            $image = Session('image') ? Session::get('image') : [];
            $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

            foreach ($convert['data']['recommendItems'] as $keyRecommendItems => $recommendItems) {
                if ($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                    $output .= '<div class="recommend__items">
                    <div class="recommend__items__title">
                    <div class="recommend__items__name">
                    <span>' . $recommendItems['homeSectionName'] . '</span>
                    </div>
                    <a href="' . route('moremovie', ['page' => $req->page, 'id' => $keyRecommendItems]) . '" class="recommend__items__btn">  
                    <h1> Xem thêm </h1>
                    <svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    </a>
                    </div>
                    <div class="recommend__item">';
                    foreach ($recommendItems['recommendContentVOList'] as $key => $movie) {
                        if ($key < 12) {
                            $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
                            if (!file_exists($urlImage)) {
                                $urlImage = $movie['imageUrl'];
                                $image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
                            }
                            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                            if ($movie_check == null) {
                                $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
                            }
                            $route = $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
                            $output .=     '<a href="' . $route . '" class="card__film"> 
                            <img class="image" src="' . asset($urlImage) . '" />
                            <p class="film__name">' . $movie['title'];
                            if (isset($movie_check->year) && $movie_check->year != '') {
                                $output  .= " (" . $movie_check->year . ")";
                            }
                            $output .= '</p>
                            </a>';
                        }
                    }
                    $output .= '</div>
                    </div>';
                }
            }
            $req->session()->put('image', $image);
            Session()->put('movie_list', $movie_list);
        }

        $output .= '<div class="text-center">
                <div class="lds-facebook"><div></div><div></div><div></div></div>
            </div>';

        $data = [$output, $req->page + 1];

        return response()->json($data);
    }
    public function searchMovieCategory($id)
    {
        return view('');
    }

    public function getFirstHomeAjax()
    {
        $movieService = new \App\Services\MovieService();

        $url_movie = 'https://ga-mobile-api.loklok.tv/cms/app/homePage/getHome?page=0';
        $movie_home = $movieService->getData($url_movie);
        while ($movie_home == null) {
            $movie_home = $movieService->getData($url_movie);
        }

        $url_top = 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchLeaderboard';
        $top_search = $movieService->getData($url_top);
        while ($top_search == null) {
            $top_search = $movieService->getData($url_top);
        }

        $output = '';

        $output .= '<div class="listfilm">';
        foreach ($movie_home['recommendItems'] as $keyRecommendItems => $recommendItems) {
            if ($recommendItems['homeSectionType'] == 'BANNER' && sizeof($recommendItems['recommendContentVOList']) > 1) {
                $output .= '<div class="listfilm__top">
                <div class="categorys">
                    <a href="' . route('search_advanced', 'a1bc2d') . '" class="home__category">Phim hành động</a>
                    <a href="' . route('search_advanced', 'a1bc7d') . '" class="home__category">Khoa học viễn tưởng</a>
                    <a href="' . route('search_advanced', 'a1bc5d') . '" class="home__category">Hoạt hình</a>
                    <a href="' . route('search_advanced', 'a1bc8d') . '" class="home__category">Kinh dị</a>
                    <a href="' . route('search_advanced', 'a1bc9d') . '" class="home__category">Hài kịch</a>
                    <a href="' . route('search_advanced', 'a1bc17d') . '" class="home__category">Thảm khốc</a>
                    <a href="' . route('search_advanced', 'a1bc15d') . '" class="home__category">Chiến tranh</a>
                </div>
                <div class="swiper__slider">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">';
                foreach ($recommendItems['recommendContentVOList'] as $key => $banner) {
                    $url = $banner['jumpAddress'];
                    $id = substr($url, strpos($url, '=') + 1, strpos($url, '&') - strpos($url, '=') - 1);
                    $category = substr($url, strpos($url, 'type=') + 5);

                    $movie_banner = Movie::where('id', intval($id))->where('category', intval($category))->first();          
                    $movie_banner_url = is_null($movie_banner) ? route('movie.detail', ['id' => $id, 'category' => $category, 'name' => $banner['title']]) : route('detail_name', $movie_banner->slug);
                    $output .= '<div class="swiper-slide rounded-xl">
							<a href="' . $movie_banner_url . '"> <img class="banner_img" src="' . $banner['imageUrl'] . '" alt="image" /> </a>
						</div>';
                }
                $output .= '</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>';
            }
            if ($recommendItems['homeSectionType'] == 'SINGLE_ALBUM') {
                $output .= '<div class="recommend__items">
			<div class="recommend__items__title">
				<div class="recommend__items__name">
					<span>' . $recommendItems['homeSectionName'] . '</span>
				</div>

				<a href="' . route('moremovie', ['page' => 0, 'id' => $keyRecommendItems]) . '" class="recommend__items__btn">
					Xem thêm
					<svg xmlns="http://www.w3.org/2000/svg" class="arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
			<div class="recommend__item">';
                $image = Session('image') ? Session::get('image') : [];
                $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

                foreach ($recommendItems['recommendContentVOList'] as $key => $movie) {
                    if ($key < 6) {
                        $output .= '<a href="';
                        $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                        $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
                        $output .= '" class="card__film">';

                        $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
                        if (!file_exists($urlImage)) {
                            $urlImage = $movie['imageUrl'];
                            $image[$movie['category'] . $movie['id']] = $movie['imageUrl'];
                        }
                        $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
                        if ($movie_check == null) {
                            $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['title']];
                        } else {
                            if (isset($movie_check->year) && $movie_check->year != '') {
                                $year = $movie_check->year;
                            }
                        }
                        $output .= '<img class="image" src="' . asset($urlImage) . '" alt="image" />
					            <p class="film__name">' . $movie['title'];
                        if (isset($movie_check->year) && $movie_check->year != '') {
                            $output  .= " (" . $movie_check->year . ")";
                        }
                        $output .= '</p>
					            </a>';
                    }
                }
                Session()->put('image', $image);
                Session()->put('movie_list', $movie_list);
                $output .= '</div>
                </div>';
            }
        }
        $output .= '
            <div class="text-center">
                <div class="lds-facebook">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>';

        $output .= '<div class="top_search">
		<div class="top_search__title">Top tìm kiếm</div>';
        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

        foreach ($top_search['list'] as $movie) {
            $output .= '<a href="';
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['domainType'], 'id' => $movie['id'], 'name' => $movie['title']]) : route('detail_name', $movie_check->slug);
            $output .= '" class="top_search__card">';

            $urlImage = 'img/' . $movie['domainType'] . $movie['id'] . 'top_search.jpg';
            if (!file_exists($urlImage)) {
                $urlImage = $movie['cover'];
                $image[$movie['domainType'] . $movie['id'] . 'top_search'] = $movie['cover'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['domainType'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['domainType'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['domainType'], 'name' => $movie['title']];
            }
            $output .=
                '<img src="' . asset($urlImage) . '" class="top_search__card__img">
			<div class="top_search__card__name">' . $movie['title'] . '</div>
		</a>';
        }
        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);
        $output .= '</div>';

        return response()->json($output);
    }
}
