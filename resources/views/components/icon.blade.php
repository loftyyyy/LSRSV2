@props(['name', 'class' => '', 'color' => null])

@php
    $svgContent = file_get_contents(resource_path("icons/{$name}.svg"));
    
    // Replace hardcoded colors with white if color is not specified or is 'white'
    if ($color === null || $color === 'white') {
        $svgContent = preg_replace('/stroke="#[^"]*"/', 'stroke="currentColor"', $svgContent);
        $svgContent = preg_replace('/fill="#[^"]*"/', 'fill="currentColor"', $svgContent);
        $svgContent = preg_replace('/stroke:#[^;"]*/', 'stroke:currentColor', $svgContent);
        $svgContent = preg_replace('/fill:#[^;"]*/', 'fill:currentColor', $svgContent);
    }
    
    // Add class attribute if provided
    if ($class) {
        $svgContent = preg_replace('/<svg([^>]*)>/', '<svg$1 class="' . $class . '">', $svgContent);
    }
@endphp

{!! $svgContent !!}