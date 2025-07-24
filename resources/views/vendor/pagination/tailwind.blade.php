@if ($paginator->hasPages())
<nav role="navigation" aria-label="Navigation de pagination" class="flex items-center justify-center">
    <div class="flex justify-center items-center space-x-1">
        {{-- Lien page précédente --}}
        @if ($paginator->onFirstPage())
        <span
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-default leading-5 rounded-lg">
            Précédent
        </span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 leading-5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white transition-all duration-200 transform hover:scale-105">
            Précédent
        </a>
        @endif

        {{-- Éléments de pagination --}}
        @foreach ($elements as $element)
        {{-- Séparateur "Trois points" --}}
        @if (is_string($element))
        <span
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">{{
            $element }}</span>
        @endif

        {{-- Tableau de liens --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <span aria-current="page"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-purple-600 border border-purple-600 cursor-default leading-5 rounded-lg shadow-sm">{{
            $page }}</span>
        @else
        <a href="{{ $url }}"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 leading-5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white transition-all duration-200 transform hover:scale-105"
            aria-label="Aller à la page {{ $page }}">{{ $page }}</a>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Lien page suivante --}}
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 leading-5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white transition-all duration-200 transform hover:scale-105">
            Suivant
        </a>
        @else
        <span
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-default leading-5 rounded-lg">
            Suivant
        </span>
        @endif
    </div>
</nav>
@endif
