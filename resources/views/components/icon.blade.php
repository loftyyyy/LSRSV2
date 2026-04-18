@props(['name', 'class' => '', 'color' => null])

@php
    $path = resource_path("icons/{$name}.svg");
    
    if (!file_exists($path)) {
        $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>';
    } else {
        $svgContent = file_get_contents($path);
    }
    
    if ($color === null || $color === 'white') {
        $svgContent = preg_replace('/stroke="#[^"]*"/', 'stroke="currentColor"', $svgContent);
        $svgContent = preg_replace('/fill="#[^"]*"/', 'fill="currentColor"', $svgContent);
        $svgContent = preg_replace('/stroke:#[^;"]*/', 'stroke:currentColor', $svgContent);
        $svgContent = preg_replace('/fill:#[^;"]*/', 'fill:currentColor', $svgContent);
    }
    
    if ($class) {
        $svgContent = preg_replace('/<svg([^>]*)>/', '<svg$1 class="' . $class . '">', $svgContent);
    }
@endphp

{!! $svgContent !!}
