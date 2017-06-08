@extends('frontend.layout')
@section('content')
    <div class="container article-category">
        <h1 class="article-category-name"><i class="fa fa-list"></i> {{ $article_category->name }}</h1>
        <div class="pagination-widget" data-range-size="6" data-base-url="{{ $article_category->slug }}"
             data-current-page="{{ $page }}" data-page-count="{{ $page_count }}"></div>
        <ul class="list-group article-list">
            @foreach($articles as $article)
                <li class="list-group-item">
                    <h4><a href="/articles/{{ $article->slug }}">{{ $article->title }}</a></h4>
                    <p class="timestamp"><i class="fa fa-clock-o"></i> Date Written: {{ $article->created_at->format('F d, Y') }}</p>
                    <p class="summary">{{ $article->summary }}</p>
                </li>
            @endforeach
        </ul>
        <div class="pagination-widget" data-range-size="6" data-base-url="{{ $article_category->slug }}"
             data-current-page="{{ $page }}" data-page-count="{{ $page_count }}"></div>
    </div>
@endsection