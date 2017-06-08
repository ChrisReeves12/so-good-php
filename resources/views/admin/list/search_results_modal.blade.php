<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-search"></i> Search Results</h4>
        </div>
        <div class="modal-body">
            @if($response['system_error'])
                <p class="error"><i class="fa fa-warning"></i> {{ $response['system_error'] }}</p>
            @elseif(!empty($response['results']))
                @foreach($response['results'] as $result)
                    <div class="search-result">
                        @if(!empty($result['image']))
                            <a href="{{ $result['link'] }}">
                                <img class="result-image" src="{{ $result['image'] }}"/>
                             </a>
                        @endif
                        <div class="result-name-section">
                            <p class="result-name"><a href="{{ $result['link'] }}">{{ $result['name'] }}</a></p>
                            <p class="result-info">{{ 'ID: ' . $result['id'] }}</p>
                            @if(!empty($result['extra_info']))
                                @foreach($result['extra_info'] as $key => $value)
                                    <p class="result-info">{{ ucfirst($key) . ': ' . $value }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <h4>No results found...</h4>
            @endif
        </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>