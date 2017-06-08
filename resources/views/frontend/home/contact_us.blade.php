@extends('frontend.layout')
@section('content')
    <div class="container contact-us">
        <div class="row">
            <div class="col-lg-5">
                <h1>Contact Us</h1>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li style="line-height: 30px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p>Please email us at info@sogoodbb.com or use the contact form. For returns, please read
                    our Return Policy here.
                </p>
                <p>{{ business('store_address')['line_1'] }} {{ business('store_address')['city'] }}, {{ business('store_address')['state'] }} {{ business('store_address')['zip'] }}<br/>
                    T: {{ business('store_phones')[0] }}<br/>
                    Mon - Fri: 9 am – 6 pm(EST)</p>
                <h1 style="margin-top: 30px;">Follow Us</h1>
                <ul>
                    <li><i class="fa fa-facebook-square"></i> <a href="https://facebook.com/sogoodbb">facebook.com/sogoodbb</a></li>
                    <li><i class="fa fa-instagram"></i> <a href="https://instagram.com/sogoodbb_">@sogoodbb</a></li>
                    <li><i class="fa fa-twitter-square"></i> <a href="https://twitter.com/sogoodbb">@sogoodbb</a></li>
                    <li><i class="fa fa-tumblr-square"></i> <a href="https://sogoodbeauty.tumblr.com">sogoodbeauty.tumblr.com</a></li>
                </ul>
            </div>
            <div class="col-lg-7">
                <div id="contact_page_contact_form">
                    <form method="post" action="/contact-us">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" placeholder="Name"/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" placeholder="Email"/>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="form-control" placeholder="Message"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">SUBMIT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection