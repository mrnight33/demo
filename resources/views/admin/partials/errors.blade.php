@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>出错了!</strong>
        <ul>
            {{json_encode($errors)}}
            @foreach ($errors->all() as $error)
                <li>{{ json_encode($error) }}</li>
            @endforeach
        </ul>
    </div>
@endif