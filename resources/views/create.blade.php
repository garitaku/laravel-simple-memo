@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            新規メモ作成
        </div>
        {{-- {{ route('store') }}と書くと→/store --}}
        {{-- {{}}のなかでbladeテンプレートの中でphpの関数や変数を展開できる --}}
        <form class="card-body my-card-body" action="{{ route('store') }}" method="POST">
            {{-- @csrf:なりすまし防止(フォームを使用する場合必要) --}}
            @csrf
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力"></textarea>
            </div>

            {{-- エラー処理(contentでエラーが出たら、、、) --}}
            @error('content')
                <div class="alert alert-danger">メモ内容を入力してください！</div>
            @enderror
            @foreach ($tags as $tag)
                <div class="form-check form-check-inline mb-3">
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $tag['id'] }}"
                        value="{{ $tag['id'] }}">
                    <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}</label>
                </div>
            @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
@endsection
