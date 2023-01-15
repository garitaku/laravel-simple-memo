@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            新規メモ作成
        </div>
        {{-- {{ route('store') }}と書くと→/store --}}
        {{-- {{}}のなかでbladeテンプレートの中でphpの関数や変数を展開できる --}}
        <form class="card-body" action="{{ route('store') }}" method="POST">
            {{-- @csrf:なりすまし防止(フォームを使用する場合必要) --}}
            @csrf
            <div class="">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
@endsection
