@if(tabComponemts($table))
    <section id="basic-tabs-components">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach(tabComponemts($table) as $key => $value)
                                    <li class="nav-item">
                                        <a class="nav-link {{($key == 'overview') ? 'active' : ''}}" id="{{$key}}-tab" data-toggle="tab" href="#{{$key}}" aria-controls="home" role="tab" aria-selected="true">{{__($table.'-'.$key)}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
