<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Services\MovieService;
use App\Exceptions\PageException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class MovieController extends Controller
{
    public function getMovieUpdate(Request $req, $name)
    {
        $movie = Movie::where('slug', $name)->first();
        if ($movie == null) {
            throw new PageException();
        }

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $movie->id . '&category=' . $movie->category;
        $movie_detail = $movieService->getData($url);

        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        $sub = '';
        $sub_en = '';

        foreach ($movie_detail['episodeVo'] as $key_episodeVo => $episodeVo) {
            $checksub_vi = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'vi') {
                        $checksub_vi = true;
                        $sub .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_vi) {
                    $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }

            $checksub_en = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'en') {
                        $checksub_en = true;
                        $sub_en .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_en) {
                    $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }
        }


        $movie->sub = $sub;
        $movie->sub_en = $sub_en;

        if (isset($req->description)) {
            $movie->description = $req->description;
        }

        $movie->save();

        return redirect()->route('detail_name', $movie->slug);
    }

    public function postMovieUpdate(Request $req, $name)
    {
        $movie = Movie::where('slug', $name)->first();
        if ($movie == null) {
            throw new PageException();
        }

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $movie->id . '&category=' . $movie->category;
        $movie_detail = $movieService->getData($url);

        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        $sub = '';
        $sub_en = '';

        foreach ($movie_detail['episodeVo'] as $key_episodeVo => $episodeVo) {
            $checksub_vi = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'vi') {
                        $checksub_vi = true;
                        $sub .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_vi) {
                    $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }

            $checksub_en = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'en') {
                        $checksub_en = true;
                        $sub_en .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_en) {
                    $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }
        }


        $movie->sub = $sub;
        $movie->sub_en = $sub_en;

        if (isset($req->description)) {
            $movie->description = $req->description;
        }

        $movie->save();

        return redirect()->route('detail_name', $movie->slug);
    }

    public function getMovie($id, $category, $name)
    {
        $movie = Movie::where('id', $id)->where('category', $category)->first();

        if (is_null($movie)) {
            $movie = new Movie();

            $str = $name;
            $movie->id = $id;
            $movie->category = $category;

            $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
            $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
            $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
            $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
            $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
            $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
            $str = preg_replace("/(đ)/", 'd', $str);
            $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
            $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
            $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
            $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
            $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
            $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
            $str = preg_replace("/(Đ)/", 'D', $str);
            $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^|\/|\:)/", '-', $str);
            $str = preg_replace("/( )/", '-', $str);
            $str = preg_replace("/(---)/", '-', $str);
            $str = preg_replace("/(--)/", '-', $str);
            $str = strtolower($str);

            if (substr($str, strlen($str) - 1, 1) == '-') {
                $str = substr($str, 0, strlen($str) - 1);
            }

            $str .= '-full-hd-vietsub.html';
            $movie->slug = $str;
            $movie->save();
        } 

        return redirect()->route('detail_name', $movie->slug);
    }

    public function getMovieByName($name)
    {
        $movie_detail = Movie::where('slug', $name)->first();
        if ($movie_detail == null) {
            throw new PageException();
        }
        if ($movie_detail->is_change_slug === 0) {
            $pos = strpos($movie_detail->slug, '.html');
            $movie_detail->update(['is_change_slug' => 1, 'slug' => substr($movie_detail->slug, 0, $pos) . '-' . $movie_detail->category . $movie_detail->id . substr($movie_detail->slug, $pos)]);
            $movie_detail->save();

            return redirect()->route('detail_name', $movie_detail->slug);
        }

        $episode_id = 0;
        $url = route('detail_name', $name);

        $start_pos = strpos($movie_detail->sub, '-' . $episode_id . '-') + strlen($episode_id) + 2 + 35;
        $end_pos = strpos($movie_detail->sub, '+' . $episode_id . '+');

        $sub = '';
        if ($start_pos < $end_pos) {
            $sub = substr($movie_detail->sub, $start_pos, $end_pos - $start_pos);
        }

        $start_pos_en = strpos($movie_detail->sub_en, '-' . $episode_id . '-') + strlen($episode_id) + 2 + 35;
        $end_pos_en = strpos($movie_detail->sub_en, '+' . $episode_id . '+');

        $sub_en = '';
        if ($start_pos_en < $end_pos_en) {
            $sub_en = substr($movie_detail->sub_en, $start_pos_en, $end_pos_en - $start_pos_en);
        }
        $movie_detail->save();

        $random_movies =  Movie::inRandomOrder()->take(30)->get();

        foreach ($random_movies as $key => $movie) {
            if (!file_exists('img/' . $movie->category . $movie->id . '.jpg') || empty($movie->name)) {
                $random_movies->forget($key);
            }
        }


        return view('pages.movie', compact('episode_id', 'movie_detail', 'name', 'url', 'sub', 'sub_en', 'random_movies'));
    }

    public function getMovieByNameEposode($name, $episode_id)
    {
        --$episode_id;

        $movie_detail = Movie::where('slug', $name)->first();
        if ($movie_detail == null) {
            throw new PageException();
        }
        if ($movie_detail->is_change_slug === 0) {
            $pos = strpos($movie_detail->slug, '.html');
            $movie_detail->update(['is_change_slug' => 1, 'slug' => substr($movie_detail->slug, 0, $pos) . '-' . $movie_detail->category . $movie_detail->id . substr($movie_detail->slug, $pos)]);
            $movie_detail->save();

            return redirect()->route('detail_name', $movie_detail->slug);
        }

        $url = route('detail_name', $name);

        $start_pos = strpos($movie_detail->sub, '-' . $episode_id . '-') + strlen($episode_id) + 2 + 35;
        $end_pos = strpos($movie_detail->sub, '+' . $episode_id . '+');

        $sub = '';
        if ($start_pos < $end_pos) {
            $sub = substr($movie_detail->sub, $start_pos, $end_pos - $start_pos);
        }

        $start_pos_en = strpos($movie_detail->sub_en, '-' . $episode_id . '-') + strlen($episode_id) + 2 + 35;
        $end_pos_en = strpos($movie_detail->sub_en, '+' . $episode_id . '+');

        $sub_en = '';
        if ($start_pos_en < $end_pos_en) {
            $sub_en = substr($movie_detail->sub_en, $start_pos_en, $end_pos_en - $start_pos_en);
        }

        $random_movies =  Movie::inRandomOrder()->take(30)->get();

        foreach ($random_movies as $key => $movie) {
            if (!file_exists('img/' . $movie->category . $movie->id . '.jpg') || empty($movie->name)) {
                $random_movies->forget($key);
            }
        }

        $movie_detail->save();

        return view('pages.movie', compact('episode_id', 'movie_detail', 'name', 'url', 'sub', 'sub_en', 'random_movies'));
    }

    private function getEpisode($category, $id, $episodeId, $definition)
    {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/media/previewInfo?category=' . $category . '&contentId=' . $id . '&episodeId=' . $episodeId . '&definition=' . $definition;
        $media = $movieService->getData($url);

        while ($media == null) {
            $media = $movieService->getData($url);
        }

        return $media;
    }

    public function getEpisodeAjax(Request $req)
    {
        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $req->id . '&category=' . $req->category;
        $movie_detail = $movieService->getData($url);
        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }
        if (!empty($movie_detail['episodeVo'])) {
            $definitionList = $movie_detail['episodeVo'][$req->episode_id]['definitionList'];
            if ($req->definition == null) {
                $media = $this->getEpisode($req->category, $req->id, $movie_detail['episodeVo'][$req->episode_id]['id'], $definitionList[0]['code']);
            } else {
                $media = $this->getEpisode($req->category, $req->id, $movie_detail['episodeVo'][$req->episode_id]['id'], $req->definition);
            }
        }

        return response()->json($media);
    }

    public function getViewMovieAjax(Request $req)
    {
        $movie = Movie::where('slug', $req->name)->first();

        $movieService = new MovieService();
        $url = 'https://ga-mobile-api.loklok.tv/cms/app/movieDrama/get?id=' . $movie->id . '&category=' . $movie->category;
        $movie_detail = $movieService->getData($url);

        while ($movie_detail == null) {
            $movie_detail = $movieService->getData($url);
        }

        if ($movie->meta == '') {
            $str = $movie_detail['name'];
            $i = 0;
            $data = [];
            $output = '';
            while (strlen($str) > 0) {
                $index = strpos($str, ' ');
                if ($index == null) {
                    $data[$i] = $str;
                    $str = '';
                } else {
                    $data[$i] = substr($str, 0, $index);
                    $str = substr($str, $index + 1);
                    ++$i;
                }
            }
            $size = sizeof($data);
            if ($size > 2) {
                if ($size == 3) {
                    $pos = 2;
                } else if ($size >= 7) {
                    $pos = $size - 3;
                } else {
                    $pos = $size - 2;
                }
                for ($i = $pos; $i < $size; ++$i) {
                    for ($j = 0; $j <= $size - $i; ++$j) {
                        for ($k = $j; $k < $j + $i; ++$k) {
                            if ($k == $j + $i - 1) {
                                $output .= $data[$k] . ', ';
                            } else {
                                $output .= $data[$k] . ' ';
                            }
                        }
                    }
                }
            }

            $movie->meta = $output;
        }
        if (!str_contains($movie->meta, 'fullhd')) {
            $movie->meta = $movie->meta . $movie_detail['name'] . ' vietsub, ' . $movie_detail['name'] . ' fullhd, ' . $movie_detail['name'] . ' fullhd vietsub, ' . $movie_detail['name'];
        }
        if ($movie->description == '') {
            $movie->description = $movie_detail['introduction'];
        }
        if ($movie->name == '') {
            $movie->name = $movie_detail['name'];
        }
        if ($movie->year == '') {
            $movie->year = $movie_detail['year'];
        }
        if ($movie->rate == '') {
            $movie->rate = $movie_detail['score'];
        }
        if ($movie->image == '' || $movie->image == '1') {
            $movie->image = asset('img/' . $movie->category . $movie->id . '.jpg');
        }

        $checksub = true;

        $count_episodes = count($movie_detail['episodeVo']) - 1;
        if (!str_contains($movie->sub, '-' . $count_episodes . '-')) {
            $checksub = false;
            $sub = '';
            foreach ($movie_detail['episodeVo'] as $key_episodeVo => $episodeVo) {
                $checksub_vi = false;
                if ($episodeVo['subtitlingList'] != null) {
                    foreach ($episodeVo['subtitlingList'] as $subtitle) {
                        if ($subtitle['languageAbbr'] == 'vi') {
                            $checksub_vi = true;
                            $sub .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                        }
                    }
                    if (!$checksub_vi) {
                        $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                    }
                } else {
                    $sub .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            }
            $movie->sub = $sub;
        }

        $sub_en = '';
        foreach ($movie_detail['episodeVo'] as $key_episodeVo => $episodeVo) {
            $checksub_en = false;
            if ($episodeVo['subtitlingList'] != null) {
                foreach ($episodeVo['subtitlingList'] as $subtitle) {
                    if ($subtitle['languageAbbr'] == 'en') {
                        $checksub_en = true;
                        $sub_en .= '-' . $key_episodeVo . '-https://srt-to-vtt.vercel.app/?url=' . $subtitle['subtitlingUrl'] . '+' . $key_episodeVo . '+';
                    }
                }
                if (!$checksub_en) {
                    $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
                }
            } else {
                $sub_en .= '-' . $key_episodeVo . '-' . '+' . $key_episodeVo . '+';
            }
        }
        $movie->sub_en = $sub_en;

        $meta = $movie->meta;
        $movie->save();

        if ($req->episode_id == 0) {
            $urlMovie = route('detail_name', $movie->slug);
        } else {
            $urlMovie = route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $req->episode_id + 1]);
        }

        $movie_episodes = '';
        if ($movie_detail['episodeCount'] > 1) {
            foreach ($movie_detail['episodeVo'] as $key => $episode) {
                $movie_episodes .= '<a class="episode';
                $movie_episodes .= intval($key) == intval($req->episode_id) ? ' active' : '';

                $movie_episodes .= '" id="' . ($key + 1) . '" href="' . route('detail_name_episode', ['name' => $movie->slug, 'episode_id' => $key + 1]) . '">' . ($key + 1) . ' </a>';
            }
        }

        $movie_tag = '';
        foreach ($movie_detail['tagList'] as $item) {
            $movie_tag .= '<div class="tag__name" id_tag="' . $item['id'] . '">';
            if (Lang::has('search_advanced.detail.' . $item['name'])) {
                $movie_tag .=  __('search_advanced.detail.' . $item['name']);
            } else {
                $movie_tag .= $item['name'];
            }
            $movie_tag .= '</div>';
        }

        $output = '';


        $image = Session('image') ? Session::get('image') : [];
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];
        foreach ($movie_detail['likeList'] as $movie) {
            $output .= '<a class="similar__container" href="';

            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
            $output .= $movie_check == null ? route('movie.detail', ['category' => $movie['category'], 'id' => $movie['id'], 'name' => $movie['name']]) : route('detail_name', $movie_check->slug);

            $output .= '">';

            $urlImage = 'img/' . $movie['category'] . $movie['id'] . '.jpg';
            if (!file_exists($urlImage)) {
                $urlImage = $movie['coverVerticalUrl'];
                $image[$movie['category'] . $movie['id']] = $movie['coverVerticalUrl'];
            }
            $movie_check = Movie::where('id', $movie['id'])->where('category', $movie['category'])->first();
            if ($movie_check == null) {
                $movie_list[$movie['category'] . $movie['id']] = ['id' => $movie['id'], 'category' => $movie['category'], 'name' => $movie['name']];
            }

            $output .= '<img src="' . asset($urlImage) . '">
            <div class="similar__name">' . $movie['name'] . '</div>
        </a>';
        }
        Session()->put('image', $image);
        Session()->put('movie_list', $movie_list);

        $data = [];

        $image = asset('img/' . $movie_detail['category'] . $movie_detail['id'] . '.jpg');

        $check_episode = $movie_detail['episodeCount'] > 1;

        array_push($data, $movie_detail, $output, $meta, $image, $movie_episodes, $movie_tag, $urlMovie, $check_episode, $checksub);
        return response()->json($data);
    }
}
