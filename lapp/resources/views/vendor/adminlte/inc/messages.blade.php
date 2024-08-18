@if(count($errors) > 0)
<div class="alert alert-danger px-2">
    @foreach($errors->all() as $error)
    <p class="mb-0"><i class="fas fa-minus-circle"></i> {{$error}}</p>
    @endforeach
</div>
@endif @if(Session::has('success'))
<div class="alert alert-success px-2">
    <p class="mb-0"><i class="fas fa-check-circle"></i> {!! Session::get('success') !!}</p>
</div>
@endif