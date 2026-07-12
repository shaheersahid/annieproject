@props([
    'title' => '',
    'items' => []
])

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18 text-capitalize">{{ $title }}</h4>
            <div class="page-title-right">
                @if(isset($slot) && $slot->isNotEmpty())
                    {{ $slot }}
                @else
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        @foreach($items as $item)
                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                @if(!$loop->last && isset($item['url']))
                                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                @else
                                    {{ $item['label'] }}
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</div>
