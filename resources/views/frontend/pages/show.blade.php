@extends('layouts.app')

@section('content')
    <div class="container section">
        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">{{ $page->title }}</h1>

        <div class="card" style="padding: 40px;">
            <div style="line-height: 1.8; color: #374151;">
                {!! $page->content !!}
            </div>
        </div>
    </div>
@endsection