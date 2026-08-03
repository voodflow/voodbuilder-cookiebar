@props([
    'category' => 'analytics',
])

@php
    $category = strtolower(trim((string) $category));
    if ($category === '') {
        $category = 'analytics';
    }
@endphp

{{--
  Gate arbitrary HTML/JS until the visitor accepts this category.

  Usage:
    <x-vcookiebar::gated category="analytics">
      <script src="https://…"></script>
    </x-vcookiebar::gated>

  Or mark scripts yourself:
    <script type="text/plain" data-vcookiebar="marketing" src="…"></script>
--}}
<template data-vcookiebar="{{ $category }}">
    {{ $slot }}
</template>
