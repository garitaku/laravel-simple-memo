<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    public function getMyMemo()
    {
        $query_tag = \Request::query(); //queryの値を取ってくる(urlのとこ)
        // =======ベースのメソッド======== sql文を途中で止めて一回変数に入れて、分岐のところから続きをするイメージ
        $query = Memo::query()->select('memos.*') //めもてーぶるの全部
            ->where('user_id', '=', \Auth::id()) //自分のゆーざーIDで絞り込み
            ->whereNull('deleted_at') //かつデリートされてないメモ
            ->orderBy('updated_at', 'DESC'); //並び順 ASC=小さい順、DESC=大きい順(新しいもの順)
        // =======ベースのメソッドここまで========

        //もしクエリパラメータtagがあれば絞り込み(なければそのまま$query->get()へGO!!)
        if (!empty($query_tag)) { //タグで絞り込み
            $query
                ->leftjoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id') //メモテーブルとメモタグテーブルをひっつける
                ->where('memo_tags.tag_id', '=', $query_tag); //メモタグてーぶるのタグidがクエリと一致するもので絞り込み
        }

        $memos = $query->get();
        return $memos;
    }
}
