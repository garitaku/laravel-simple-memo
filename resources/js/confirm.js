function deleteHandle(e) {
    if (window.confirm('本当に削除していいですか?')) {//削除押された時に
        document.getElementById('delete-form').submit();
    } else {
        alert('キャンセルしました');
    }
}