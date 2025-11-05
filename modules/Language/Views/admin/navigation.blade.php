@php
    $user = Auth::user();
    $languages = \Modules\Language\Models\Language::getActive();
@endphp

