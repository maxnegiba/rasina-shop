@extends('layouts.app')

@section('seo_meta')
    {!! seo($page) !!}
@endsection

@section('content')
<div class="bg-ivory min-h-screen pt-20 pb-24">
    <!-- Breadcrumbs -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <nav class="flex text-[10px] uppercase tracking-[0.2em] text-dark-brown/50" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') ?? '/' }}" class="hover:text-vintage-gold transition-colors">Acasă</a>
                </li>
                <li>
                    <span class="mx-2">/</span>
                </li>
                <li class="text-dark-brown font-medium" aria-current="page">
                    {{ $page->title }}
                </li>
            </ol>
        </nav>
    </div>

    <!-- Article Header -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-16">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-dark-brown mb-8 leading-tight">
            {{ $page->title }}
        </h1>
        <div class="w-12 h-px bg-vintage-gold mx-auto"></div>
    </div>

    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="prose prose-stone max-w-none font-light leading-loose text-dark-brown/80 prose-headings:font-serif prose-headings:text-dark-brown prose-headings:font-normal prose-a:text-vintage-gold prose-a:no-underline hover:prose-a:text-dark-brown prose-img:rounded-sm">
            {!! $page->content !!}
        </article>
    </div>
</div>
@endsection
