@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            メモ編集
            <form class="card-body" action="{{ route('destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="memo_id" value="{{ $edit_memo['id'] }}">
                <button type="submit">削除</button>
            </form>
        </div>
        {{-- {{ route('store') }}と書くと→/store --}}
        {{-- {{}}のなかでbladeテンプレートの中でphpの関数や変数を展開できる --}}
        <form class="card-body" action="{{ route('update') }}" method="POST">
            {{-- @csrf:なりすまし防止(フォームを使用する場合必要) --}}
            @csrf
            {{-- 更新に必要なメモIDをhiddenで隠し持って教えてあげる --}}
            <input type="hidden" name="memo_id" value="{{ $edit_memo['id'] }}">
            <div class="">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $edit_memo['content'] }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection
