@if($tab == 'categories')

@include('components.category.manage-category')

@else
<div>
    @include('components.category.select-categories')
</div>

@endif