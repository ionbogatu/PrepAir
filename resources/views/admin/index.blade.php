@extends ('layouts.admin')

@section ('content')
    @if (session('response'))
    <h3 class="
        @if (session('response_type') == 'success')
            response_success
        @elseif (session('response_type') == 'error')
            response_error
        @endif
            ">
        {{ session('response') }}
    </h3>
    @endif
    <a class="action db_import" href="/admins/db/import">Database import</a>
    <a class="action event_create" href="/admins/event/create">Create<br/>event</a>
@endsection