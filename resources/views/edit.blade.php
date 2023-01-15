@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            メモ編集
            <form class="card-body" action="{{ route('destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
                <button type="submit">削除</button>
            </form>
        </div>
        {{-- {{ route('store') }}と書くと→/store --}}
        {{-- {{}}のなかでbladeテンプレートの中でphpの関数や変数を展開できる --}}
        <form class="card-body" action="{{ route('update') }}" method="POST">
            {{-- @csrf:なりすまし防止(フォームを使用する場合必要) --}}
            @csrf
            {{-- 更新に必要なメモIDをhiddenで隠し持って教えてあげる --}}
            <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
            <div class="">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $edit_memo[0]['content'] }}</textarea>
            </div>
            @foreach ($tags as $tag)
                <div class="form-check form-check-inline mb-3">
                    {{-- 3項演算子を使用して、タグのチェックをつける{{条件 ? true : false}} --}}
                    {{-- もし$include_tagsにループで回っているタグのidが含まれればchecked --}}
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $tag['id'] }}"
                        value="{{ $tag['id'] }}" {{ in_array($tag['id'], $include_tags) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}</label>
                </div>
            @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection
