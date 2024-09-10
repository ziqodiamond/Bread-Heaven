@props(['active' => false])

<a {{ $attributes }}
    class= "{{ $active ? '☐ bg-orange-900 text-white' : 'text-gray-800 hover:bg-gray-300 hover:text-gray-800' }} rounded-md px-3 py-2 text-sm font-medium"
    aria-current= "{{ $active ? 'page' : false }}">{{ $slot }}</a>
