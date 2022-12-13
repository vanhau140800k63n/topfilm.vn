<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use App\Models\Movie;

class StorageController extends Controller
{
    public function saveImage(Request $req)
    {
        $image = Session('image') ? Session::get('image') : [];
        $index = 0;
        foreach ($image as $key => $url) {
            if (!file_exists('img/' . $key . '.jpg')) {
                if ($url != "") {
                    $url = str_replace(' ', '%20', $url);
                    $size = getimagesize($url);
                    if ($size[0] < $size[1] || str_contains($key, 'top_search')) {
                        $url = file_get_contents($url);
                        $imgFile = Image::make($url);
                        $imgFile->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $imgFile->save('img/' . $key . '.jpg');

                        ++$index;
                    }
                }
            }
            unset($image[$key]);

            if ($index == 10) {
                break;
            }
        }

        $req->session()->put('image', $image);
        return response()->json(true);
    }

    public function saveMovie(Request $req)
    {
        $movie_list = Session('movie_list') ? Session::get('movie_list') : [];

        foreach ($movie_list as $key => $data) {
            $movie_check = Movie::where('id', $data['id'])->where('category', $data['category'])->first();
            if ($movie_check == null) {
                $movie = new Movie();
                $movie->id = $data['id'];
                $movie->category = $data['category'];
                $movie->meta = '';

                $str =  $data['name'];
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

                $str .= '-full-hd-vietsub'. '-' . $movie->category . $movie->id .'.html';
                $movie->slug = $str;
                $movie->save();
            }
            unset($movie_list[$key]);
        }
        $req->session()->put('movie_list', $movie_list);
        return response()->json(true);
    }
}
