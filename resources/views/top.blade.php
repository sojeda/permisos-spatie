@extends('app')

@section('htmlheader_title')
    Home
@endsection


@section('main-content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
					PUEDES VER LA PAGINA SECRETA
				</div>

				@role('writer')
				<div class="panel-body">
					I'm a writer!
				</div>				
				@else
				<div class="panel-body">
					I'm not a writer!
				</div>				
				@endrole
				
				@if(Auth::user()->hasPermissionTo('edit articles'))	
				<div class="panel-body">
					Permisos
				</div>
				@endif
				<a class="btn btn-warning" href="{{ route('asignar', Auth::user()->id) }}">Asignar</a>				


			</div>
		</div>
	</div>
</div>
@endsection
