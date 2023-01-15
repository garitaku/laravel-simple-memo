<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

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
        //ここでメモデータを取得
        $memos = Memo::select('memos.*') //めもてーぶるの全部
            ->where('user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
            ->whereNull('deleted_at') //かつデリートされてないメモ
            ->orderBy('updated_at', 'DESC') //並び順 ASC=小さい順、DESC=大きい順(新しいもの順)
            ->get();
        // dd($memos);

        return view('create', compact('memos')); //viewに取得したメモデータを渡す
    }

    public function edit($id)
    {
        //ここでメモデータを取得
        $memos = Memo::select('memos.*') //めもてーぶるの全部
            ->where('user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
            ->whereNull('deleted_at') //かつデリートされてないメモ
            ->orderBy('updated_at', 'DESC') //並び順 ASC=小さい順、DESC=大きい順(新しいもの順)
            ->get();
        // dd($memos);
        $edit_memo = Memo::find($id); //編集するメモを一つだけ取得する

        return view('edit', compact('memos', 'edit_memo')); //viewに取得したメモデータを渡す
    }

    public function store(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts);//dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        Memo::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        return redirect(route('home'));
    }

    public function update(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts);//dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        Memo::where('id', '=', $posts['memo_id'])->update(['content' => $posts['content']]);

        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all(); //formで投げられた内容を全て取得する
        // dd($posts); //dump dieの略、メソッドの引数のとった値を展開して止める、デバッグ関数
        // dd(\Auth::id());ログインユーザーIDが取れているかの確認
        Memo::where('id', '=', $posts['memo_id'])
            ->update(['deleted_at' => date("Y-m-d H:i:s", time())]);//論理削除

        return redirect(route('home'));
    }
}
