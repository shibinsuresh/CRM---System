@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Posts</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@foreach ($posts as $post)
<div class="card mb-3">
    <div class="card-body">
        <h5>{{ $post->title }}</h5>
        <p>{{ $post->content }}</p>
        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-sm">Edit</a>
        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>
</div>
@endforeach

{{ $posts->links() }}
@endsection
