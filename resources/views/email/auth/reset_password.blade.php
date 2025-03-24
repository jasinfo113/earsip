<div style="padding-bottom: 30px">
    Anda menerima email ini karena kami menerima permintaan untuk reset password untuk akun Anda.
    <br /><br />
    Silahkan klik tombol dibawah ini untuk melakukan reset password:
</div>
<div style="padding-bottom: 40px; text-align:center;">
    <a href="{{ $url }}" rel="noopener"
        style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#36328d;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle"
        target="_blank">
        Reset Password
    </a>
</div>
<div style="padding-bottom: 30px">
    Link ini hanya berlaku selama <strong>30 menit</strong> dan akan kedaluwarsa pada
    <strong>{{ $expired_at }}</strong>.
    <br /><br />
    ** <i>Jika Anda tidak melakukan permintaan reset password, tidak ada tindakan lebih lanjut yang diperlukan.</i>
</div>
<div style="padding-bottom: 20px; word-wrap: break-all;">
    <p style="margin-bottom: 10px;">
        Tombol tidak berfungsi?<br />
        Silahkan salin tautan ini dan masukan ke browser Anda:
    </p>
    <a href="{{ $url }}" rel="noopener" target="_blank" style="text-decoration:none;color:#36328d;">
        {{ $url }}
    </a>
</div>
