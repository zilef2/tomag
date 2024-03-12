@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'empresas.store']) !!}

		<div class="mb-3">
			{{ Form::label('nombre', 'Nombre', ['class'=>'form-label']) }}
			{{ Form::text('nombre', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('NIT', 'NIT', ['class'=>'form-label']) }}
			{{ Form::text('NIT', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop