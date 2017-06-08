@extends('admin.layout')
@section('content')
    <div class="container list-page">
        <h1><i class="fa fa-list"></i> {{ $record_type->formal_name }}</h1>
        <div data-list-type="{{ $record_type->name }}" data-list-name="{{ $record_type->formal_name }}" id="admin_list_search"></div>
        {{ $paginator->links() }}
        <table class="table">
            <tr>
                <th> </th>
                @if($can_be_duplicated)
                    <th> </th>
                @endif
                <th> </th>
                <th>ID</th>
                @foreach($record_type->record_fields as $record_field)
                    <th>{{ $record_field->name }}</th>
                @endforeach
            </tr>
            @foreach($record_data as $record)
                <tr>
                    <td><a href="{{ $record_type->edit_url . '/' . $record['ID'] }}" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a></td>
                    @if($can_be_duplicated)
                        <td><a href="" data-id="{{ $record['ID'] }}" class="btn btn-default copy-record"><i class="fa fa-copy"></i> Duplicate</a></td>
                    @endif
                    <td><a data-http-method="delete" href="/admin/record/{{ $record_type->model . '/' . $record['ID'] }}" class="btn btn-danger"><i class="fa fa-times"></i> Delete</a></td>
                    @foreach($record as $attr_name => $value)
                        <td>{!! $value !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
        {{ $paginator->links() }}
        <!-- Modal -->
        <div id="search_results_modal" class="modal fade" role="dialog"></div>
    </div>
@endsection
@section('footer-scripts')
<script>
    $('a.copy-record').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/record/copy/{{ $record_type->model  }}/" + e.target.dataset.id,
            method: 'POST',
            dataType: 'json',
            timeout: 5000,
            data: {_token: "{{ csrf_token() }}"},
            complete: function(res) {
                if(res.status === 200)
                {
                    if(res.responseJSON.system_error)
                    {
                        window.alert(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = res.responseJSON.edit_url;
                    }
                }
                else
                {
                    window.alert('Timeout while trying to copy the record, please try again.');
                }
            }
        });
    });
</script>
@endsection