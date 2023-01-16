@extends('layouts.app')
@section('javascript')
    {{-- editの時のみこのjsファイルを読み込む --}}
    <script src="/js/confirm.js"></script>
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            メモ編集
            <form class="m-0" id="delete-form" action="{{ route('destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
                <i class="bi bi-trash mr-3" onclick="deleteHandle(event);"></i>
            </form>
        </div>
        {{-- {{ route('store') }}と書くと→/store --}}
        {{-- {{}}のなかでbladeテンプレートの中でphpの関数や変数を展開できる --}}
        <form class="card-body my-card-body" action="{{ route('update') }}" method="POST">
            {{-- @csrf:なりすまし防止(フォームを使用する場合必要) --}}
            @csrf
            {{-- 更新に必要なメモIDをhiddenで隠し持って教えてあげる --}}
            <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $edit_memo[0]['content'] }}</textarea>
            </div>
            {{-- エラー処理 --}}
            @error('content')
                <div class="alert alert-danger">メモ内容を入力してください！</div>
            @enderror
            {{-- エラー処理ここまで --}}
            @foreach ($tags as $tag)
                <div class="form-check form-check-inline mb-3">
                    {{-- 3項演算子を使用して、タグのチェックをつける{{条件 ? true : false}} --}}
                    {{-- もし$include_tagsにループで回っているタグのidが含まれればchecked --}}
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $tag['id'] }}"
                        value="{{ $tag['id'] }}" {{ in_array($tag['id'], $include_tags) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}</label>
                </div>
            @endforeach
            <input type="text" class="form-control w-50" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection
