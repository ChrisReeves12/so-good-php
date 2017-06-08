<p>Hi there,</p>
<p>
    You are receiving this message because there was a recent password recovery request made under this email address ({{ $email }}) at
    <a href="{{ business('site_url') }}">{{ business('site_url') }}</a> for {{ business('store_name') }}. If you did not initiate or approve this,
   please let us know by responding to this email.
</p>
<p>To reset your password, click the link below.</p>
<a href="{{ $forgot_url }}">{{ $forgot_url }}</a>