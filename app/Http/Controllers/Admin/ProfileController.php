<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Profhistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }
    
    public function create(Request $request)
    {
      $this->validate($request, Profile::$rules);

      $profile = new Profile;
      $form = $request->all();

    //   // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
    //   if (isset($form['image'])) {
    //     $path = $request->file('image')->store('public/image');
    //     $news->image_path = basename($path);
    //   } else {
    //       $news->image_path = null;
    //   }

      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
    //   // フォームから送信されてきたimageを削除する
    //   unset($form['image']);

      // データベースに保存する
      $profile->fill($form);
      $profile->save();
      
      
      

      return redirect('admin/profile/');
  


    }
    
    public function edit(Request $request)
    {
       // Profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      if (empty($profile)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profile_form' => $profile]);
        // return view('admin.profile.edit');
    }
    
    public function update(Request $request)
    {
       // Validationをかける
      $this->validate($request, Profile::$rules);
      // Profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $profile_form = $request->all();
      // if (isset($news_form['image'])) {
      //   $path = $request->file('image')->store('public/image');
      //   $news->image_path = basename($path);
      //   unset($news_form['image']);
      // } elseif (isset($request->remove)) {
      //   $news->image_path = null;
      //   unset($news_form['remove']);
      // }
      
      unset($profile_form['_token']);

      // 該当するデータを上書きして保存する
      $profile->fill($profile_form)->save();
      
      $profhistory = new Profhistory;
      $profhistory->profile_id = $profile->id;
      $profhistory->edited_at = carbon::now();
      $profhistory->save();

      // return redirect('admin/profile');
      return redirect('admin/profile/edit');
    }
    
    public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          // 検索されたら検索結果を取得する
          $profiles = Profile::where('title', $cond_title)->get();
      } else {
          // それ以外はすべてのニュースを取得する
          $profiles = Profile::all();
      }
      return view('admin.profile.index', ['profiles' => $profiles, 'cond_title' => $cond_title]);
  }


}
