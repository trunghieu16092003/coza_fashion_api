<a href="{{ route('google') }}">Đăng nhập google</a>
<form action="{{ url('/momo_payment') }}" method="post">
    @csrf
    <input type="hidden" name="total">
    <button name="payUrl">Thanh toán momo</button>
</form>
