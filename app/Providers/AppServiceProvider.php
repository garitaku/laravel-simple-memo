<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    { //ビューコンポーザー(何しても呼ばれるやーつ)
        view()->composer('*', function ($view) { // 何回もHomeController.phpで呼ばれる為、こちらに記載
            //自分のメモ取得はMemoModelに任せた(リファクタリング作業)
            //インスタンス化してMemoモデルメソッドを呼び出す
            //Memo.phpに記述したメモ取得のメソッドを呼び出す為にメモモデルをインスタンス化
            $memo_model = new Memo();
            //メモ取得
            $memos = $memo_model->getMyMemo();

            $tags = Tag::select('tags.*')
                ->where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();
            // 'memos'という名前で$memosで持ってきたものをviewに渡す
            $view->with('memos', $memos)->with('tags', $tags);
        });
    }
}
