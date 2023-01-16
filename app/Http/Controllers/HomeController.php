<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\MemoTag;
use App\Models\Tag;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //AppServiceProvider.phpに記載した為コメントアウト
        //ここでメモデータを取得
        // $memos = Memo::select('memos.*') //めもてーぶるの全部
        //     ->where('user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
        //     ->whereNull('deleted_at') //かつデリートされてないメモ
        //     ->orderBy('updated_at', 'DESC') //並び順 ASC=小さい順、DESC=大きい順(新しいもの順)
        //     ->get();

        $tags = Tag::select('tags.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();
        // dd($tags);

        return view('create', compact('tags')); //viewに取得したメモデータを渡す
    }

    public function edit($id)
    {
        //AppServiceProvider.phpに記載した為コメントアウト->その後Memoモデルに記載

        $tags = Tag::select('tags.*') //タグテーブルの全部
            ->where('user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
            ->whereNull('deleted_at') //かつデリートされてないメモ
            ->orderBy('updated_at', 'DESC') //並び順 ASC=小さい順、DESC=大きい順(新しいもの順)
            ->get();

        // dd($memos);
        //選ばれたメモ用
        //メモテーブルとタグテーブルのidを使用
        $edit_memo = Memo::select('memos.*', 'tags.id AS tag_id') //idで名前被りが発生するためAS文を使用
            ->leftjoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id') //メモテーブルとメモタグテーブルをひっつける
            ->leftjoin('tags', 'memo_tags.tag_id', '=', 'tags.id') //メモタグテーブルとタグテーブルを引っ付ける
            ->where('memos.user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
            ->where('memos.id', '=', $id) //めもは選ばれたメモのみ
            ->whereNull('memos.deleted_at') //かつデリートされてないメモ
            ->get(); //結果として配列が帰ってくる(タグが3つの場合3つの同じメモを取得していることになる？)
        //そのためviewで表示させる場合0番目の記述が必要

        $include_tags = []; //一つのメモに対してタグは複数ある可能性があるため、配列で取得
        foreach ($edit_memo as $memo) { //ひっついてるタグの分だけforeachで回す
            array_push($include_tags, $memo['tag_id']); //そのタグの分だけ配列につっこむ
        }
        // dd($include_tags);
        //viewに取得したメモデータを渡す(メモ一覧、選ばれたメモ、選ばれたメモにひっついてるタグ(配列)、タグ一覧)
        // dd($memos, $edit_memo, $include_tags, $tags);
        return view('edit', compact('edit_memo', 'include_tags', 'tags'));
    }

    public function store(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts); //dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        
        //バリデーション(contentの中は必須やで(空やとできひんで))
        $request->validate(['content' => 'required']);
        //トランザクション開始
        DB::transaction(function () use ($posts) {
            //メモをインサートしつつIDを取得(インサートしてそのIDを変数に入れる)
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            //新規タグがすでにあるかのチェック
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', 'new_tag')
                ->exists(); //タグ一覧テーブルからそのユーザーかつ同じタグがあれば、trueを返す
            //新規タグが入力されている。かつ入力されたタグが自分のタグ一覧に存在しないなら
            //タグをインサートしつつIDを取得
            //メモタグにメモIDとタグIDをインサート(ここでめもとタグがつながる)
            if ((!empty($posts['new_tag']) || $posts['new_tag'] === "0") && !$tag_exists) {
                $tag_id = Tag::insertGetId(['name' => $posts['new_tag'], 'user_id' => \Auth::id()]);
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
            }

            //既存タグが紐づけられた場合→memo_tagsにインサート(チェックされたタグの数だけ)
            if (!empty($posts['tags'][0])) { //チェックボックスに一つもチェックが入っていなかったら
                foreach ($posts['tags'] as $tag) {
                    MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
                }
            }
        }); //トランザクション終了
        return redirect(route('home'));
    }

    public function update(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts);//dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        $request->validate(['content' => 'required']);
        //トランザクション開始
        DB::transaction(function () use ($posts) {
            Memo::where('id', '=', $posts['memo_id'])->update(['content' => $posts['content']]);
            //一旦メモとタグの紐付けを削除
            MemoTag::where('memo_id', '=', $posts['memo_id'])
                ->delete();
            //再度メモとタグの紐付け
            foreach ($posts['tags'] as $tag) {
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag]);
            }
            //もし、新しいタグの紐付けがあれば、インサートして紐づける
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])
                ->exists(); //タグ一覧テーブルからそのユーザーかつ同じタグがあれば、trueを返す
            //新規タグが入力されている。かつ入力されたタグが自分のタグ一覧に存在しないなら
            //タグをインサートしつつIDを取得
            //メモタグにメモIDとタグIDをインサート(ここでめもとタグがつながる)
            if ((!empty($posts['new_tag']) || $posts['new_tag'] === "0") && !$tag_exists) {
                $tag_id = Tag::insertGetId(['name' => $posts['new_tag'], 'user_id' => \Auth::id()]);
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag_id]);
            }
        });

        //もし、新しいタグの紐付けがあれば、インサートして紐づける
        //トランザクションここまで

        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts); //dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        Memo::where('id', '=', $posts['memo_id'])
            ->update(['deleted_at' => date("Y-m-d H:i:s", time())]); //論理削除

        return redirect(route('home'));
    }
}
