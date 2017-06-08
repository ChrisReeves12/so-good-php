@extends('frontend.layout')
@section('content')
    <div class="container article">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="article-title">{{ $article->title }}</h1>
                @if($article->article_category)
                    <h5 class="article-category"><i class="fa fa-list"></i> Category: <a href="/article-category/{{ $article->article_category->slug }}">{{ $article->category_name }}</a></h5>
                @endif
                <h5 class="article-timestamp"><i class="fa fa-clock-o"></i>
                    Written {{ $article->created_at->format('F d, Y') }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-lg-8 article-body">
                {!! $article->body !!}
            </div>
            @if(isset($other_articles) && $other_articles->count() > 0)
                <div class="col-lg-4 hidden-md-down article-recs">
                    <h4>More Articles Like This</h4>
                    <ul>
                        @foreach($other_articles as $article)
                            <li>
                                <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                                <p><i class="fa fa-clock-o"></i> Written {{ $article->created_at->format('F d, Y') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        @if(isset($other_articles) && $other_articles->count() > 0)
            <div class="row hidden-lg-up">
                <div class="col-xs-12 article-recs">
                    <h4>More Articles Like This</h4>
                    <ul>
                        @foreach($other_articles as $article)
                            <li>
                                <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                                <p><i class="fa fa-clock-o"></i> Written {{ $article->created_at->format('F d, Y') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endsection