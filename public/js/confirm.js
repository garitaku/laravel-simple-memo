function deleteHandle(event) {
    //一旦formをストップ
    event.preventDefault();
    if (window.confirm('本当に削除していいですか?')) {//削除押された時に
        //削除OKなら再開
        document.getElementById('delete-form').submit();
    } else {
        alert('キャンセルしました');
    }
}